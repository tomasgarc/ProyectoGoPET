<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use Stripe\Webhook;

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
     * Process the payment via Stripe Checkout.
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
        ]);

        $caretakerId = $request->caretaker_id;
        $amount = $careRequest->price;

        try {
            $stripe = app(StripeClient::class);

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Servicio de Cuidado de Mascotas - GoPET',
                                'description' => 'Servicio de cuidado del ' . \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y'),
                            ],
                            'unit_amount' => (int) round($amount * 100), // Stripe expects cents
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('payments.success', $careRequest) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.checkout', [$careRequest, 'caretaker_id' => $caretakerId]),
                'metadata' => [
                    'care_request_id' => $careRequest->id,
                    'caretaker_id' => $caretakerId,
                    'user_id' => auth()->id(),
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al iniciar el pago con Stripe: ' . $e->getMessage());
        }
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
     * Cancel reservation and refund owner via Stripe.
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

        try {
            $stripe = app(StripeClient::class);
            $transactionId = $payment->transaction_id;
            $paymentIntentId = null;

            // If transaction_id is a checkout session, retrieve the session to get the payment intent
            if (str_starts_with($transactionId, 'cs_')) {
                $session = $stripe->checkout->sessions->retrieve($transactionId);
                $paymentIntentId = $session->payment_intent;
            } elseif (str_starts_with($transactionId, 'pi_')) {
                $paymentIntentId = $transactionId;
            }

            if ($paymentIntentId) {
                $stripe->refunds->create([
                    'payment_intent' => $paymentIntentId,
                ]);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el reembolso en Stripe: ' . $e->getMessage());
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

    /**
     * Handle the Stripe Checkout success redirect.
     */
    public function paymentSuccess(CareRequest $careRequest, Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('payments.checkout', $careRequest)
                ->with('error', 'Sesión de pago no válida.');
        }

        try {
            $stripe = app(StripeClient::class);
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent.payment_method']
            ]);

            if ($session->payment_status === 'paid') {
                $this->registerStripePayment($session);

                return redirect()->route('care-requests.show', $careRequest)
                    ->with('success', '¡Pago procesado con éxito! La reserva se ha confirmado y el dinero está en depósito de garantía.');
            }
        } catch (\Exception $e) {
            return redirect()->route('payments.checkout', $careRequest)
                ->with('error', 'Error al verificar el pago: ' . $e->getMessage());
        }

        return redirect()->route('payments.checkout', $careRequest)
            ->with('error', 'El pago no ha sido completado.');
    }

    /**
     * Handle Stripe Webhook calls.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            // Retrieve session with payment_intent and payment_method expanded
            try {
                $stripe = app(StripeClient::class);
                $expandedSession = $stripe->checkout->sessions->retrieve($session->id, [
                    'expand' => ['payment_intent.payment_method']
                ]);
                $this->registerStripePayment($expandedSession);
            } catch (\Exception $e) {
                // Log/handle error
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Helper to register the payment in database.
     */
    private function registerStripePayment($session)
    {
        $metadata = $session->metadata;
        $careRequestId = $metadata->care_request_id ?? null;
        $caretakerId = $metadata->caretaker_id ?? null;
        $userId = $metadata->user_id ?? null;

        if (!$careRequestId || !$caretakerId || !$userId) {
            return;
        }

        // Check for duplicate payment records
        $existingPayment = Payment::where('transaction_id', $session->id)->first();
        if ($existingPayment) {
            return;
        }

        $careRequest = CareRequest::findOrFail($careRequestId);

        // Ensure we only process if it is pending
        if ($careRequest->status !== 'pending') {
            return;
        }

        $lastFour = '0000';
        try {
            $paymentIntent = $session->payment_intent;
            if ($paymentIntent && $paymentIntent->payment_method && $paymentIntent->payment_method->type === 'card') {
                $lastFour = $paymentIntent->payment_method->card->last4;
            }
        } catch (\Exception $e) {
            // Fallback
        }

        DB::transaction(function () use ($careRequest, $caretakerId, $userId, $session, $lastFour) {
            $careRequest->update([
                'status' => 'accepted',
                'accepted_by' => $caretakerId,
            ]);

            $amount = $careRequest->price;
            $fee = round($amount * 0.10, 2);
            $netAmount = $amount - $fee;

            Payment::create([
                'care_request_id' => $careRequest->id,
                'user_id' => $userId,
                'receiver_id' => $caretakerId,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'status' => 'escrow',
                'card_last_four' => $lastFour,
                'transaction_id' => $session->id,
            ]);
        });
    }
}
