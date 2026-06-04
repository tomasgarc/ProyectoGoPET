<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function checkout(CareRequest $careRequest, Request $request)
    {
        // 1. Ensure current user is the request owner
        if ($careRequest->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta página.');
        }

        // 2. Ensure request is pending
        if ($careRequest->status !== 'pending') {
            return redirect()->route('care-requests.show', $careRequest)
                ->with('error', 'Esta petición ya no está pendiente de aceptación.');
        }

        // 3. Validate and get caretaker
        $request->validate([
            'caretaker_id' => 'required|exists:users,id|different:user_id',
        ]);

        $caretaker = User::findOrFail($request->caretaker_id);

        // Calculate fees
        $amount = $careRequest->price;
        $fee = round($amount * 0.10, 2); // 10% platform fee
        $netAmount = $amount - $fee;

        return view('payments.checkout', compact('careRequest', 'caretaker', 'amount', 'fee', 'netAmount'));
    }

    /**
     * Process the simulated payment.
     */
    public function processPayment(CareRequest $careRequest, Request $request)
    {
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        if ($careRequest->status !== 'pending') {
            return redirect()->route('care-requests.show', $careRequest)
                ->with('error', 'Esta petición ya no está pendiente de aceptación.');
        }

        $request->validate([
            'caretaker_id' => ['required', 'exists:users,id', 'different:user_id'],
            'card_name' => ['required', 'string', 'min:3'],
            'card_number' => ['required', 'string', 'regex:#^[0-9\s]{13,19}$#'],
            'card_expiry' => ['required', 'string', 'regex:#^(0[1-9]|1[0-2])/?([0-9]{2})$#'],
            'card_cvv' => ['required', 'numeric', 'digits_between:3,4'],
        ], [
            'card_number.regex' => 'El número de tarjeta no es válido.',
            'card_expiry.regex' => 'La fecha de expiración debe tener el formato MM/AA.',
            'card_cvv.digits_between' => 'El CVV debe tener 3 o 4 dígitos.',
        ]);

        $caretakerId = $request->caretaker_id;

        // Start Transaction
        DB::transaction(function () use ($careRequest, $caretakerId, $request) {
            // Update Care Request
            $careRequest->update([
                'status' => 'accepted',
                'accepted_by' => $caretakerId,
            ]);

            $amount = $careRequest->price;
            $fee = round($amount * 0.10, 2);
            $netAmount = $amount - $fee;

            // Clean card number spaces
            $cleanCard = str_replace(' ', '', $request->card_number);
            $lastFour = substr($cleanCard, -4);

            // Create Payment
            Payment::create([
                'care_request_id' => $careRequest->id,
                'user_id' => auth()->id(),
                'receiver_id' => $caretakerId,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'status' => 'escrow',
                'card_last_four' => $lastFour,
                'transaction_id' => 'ch_'.Str::random(20),
            ]);
        });

        return redirect()->route('care-requests.show', $careRequest)
            ->with('success', '¡Pago procesado con éxito! La reserva se ha confirmado y el dinero está en depósito de garantía.');
    }

    /**
     * Release payment to caretaker and finalize request.
     */
    public function releasePayment(CareRequest $careRequest)
    {
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = $careRequest->payment()->where('status', 'escrow')->first();

        if (! $payment) {
            return back()->with('error', 'No se encontró ningún pago retenido para esta petición.');
        }

        DB::transaction(function () use ($careRequest, $payment) {
            // Update payment
            $payment->update([
                'status' => 'released',
            ]);

            // Update care request status
            $careRequest->update([
                'status' => 'finalized',
            ]);
        });

        return redirect()->route('care-requests.show', $careRequest)
            ->with('success', '¡Servicio completado! El pago ha sido liberado al cuidador con éxito.');
    }

    /**
     * Cancel reservation and refund owner.
     */
    public function cancelAndRefund(CareRequest $careRequest)
    {
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = $careRequest->payment()->where('status', 'escrow')->first();

        if (! $payment) {
            return back()->with('error', 'No se encontró ningún pago activo en depósito para esta petición.');
        }

        DB::transaction(function () use ($careRequest, $payment) {
            // Update payment
            $payment->update([
                'status' => 'refunded',
            ]);

            // Reset care request
            $careRequest->update([
                'status' => 'pending',
                'accepted_by' => null,
            ]);
        });

        return redirect()->route('care-requests.show', $careRequest)
            ->with('success', 'La reserva ha sido cancelada y el importe total ha sido reembolsado a tu cuenta.');
    }

    /**
     * Show user wallet.
     */
    public function wallet()
    {
        $userId = auth()->id();

        // 1. Available balance (Released earnings)
        $availableBalance = Payment::where('receiver_id', $userId)
            ->where('status', 'released')
            ->sum('net_amount');

        // 2. Escrow balance (Pending earnings)
        $escrowBalance = Payment::where('receiver_id', $userId)
            ->where('status', 'escrow')
            ->sum('net_amount');

        // 3. Total Spent (Amount paid as owner)
        $totalSpent = Payment::where('user_id', $userId)
            ->whereIn('status', ['escrow', 'released'])
            ->sum('amount');

        // 4. Transaction history
        $transactions = Payment::where('user_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['careRequest', 'user', 'receiver'])
            ->latest()
            ->get();

        return view('payments.wallet', compact('availableBalance', 'escrowBalance', 'totalSpent', 'transactions'));
    }
}
