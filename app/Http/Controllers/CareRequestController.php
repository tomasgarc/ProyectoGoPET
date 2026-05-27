<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CareRequest;
use App\Models\User;
use App\Models\Chat;

class CareRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = now()->toDateString();

        // Active requests created by the user
        $myRequests = auth()->user()->careRequests()
            ->where('status', '!=', 'finalized')
            ->where('end_date', '>=', $today)
            ->with(['dogs', 'acceptedBy'])
            ->latest()
            ->get();

        // Active requests accepted by the user
        $acceptedRequests = CareRequest::where('accepted_by', auth()->id())
            ->where('status', '!=', 'finalized')
            ->where('end_date', '>=', $today)
            ->with(['dogs', 'user'])
            ->latest()
            ->get();

        return view('care_requests.index', compact('myRequests', 'acceptedRequests'));
    }

    /**
     * Display a listing of care requests from other users.
     */
    public function explore()
    {
        $careRequests = CareRequest::where('user_id', '!=', auth()->id())
            ->where('status', 'pending')
            ->where('end_date', '>=', now()->toDateString())
            ->with(['dogs', 'user'])
            ->latest()
            ->get();

        return view('care_requests.explore', compact('careRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dogs = auth()->user()->dogs;
        return view('care_requests.create', compact('dogs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dogs' => 'required|array',
            'dogs.*' => 'exists:dogs,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $careRequest = auth()->user()->careRequests()->create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price' => $request->price,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        $careRequest->dogs()->attach($request->dogs);

        return redirect()->route('care-requests.index')->with('success', 'Petición de cuidado creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CareRequest $careRequest)
    {
        $careRequest->load(['dogs', 'user', 'acceptedBy', 'payment']);
        
        $users = [];
        if ($careRequest->user_id === auth()->id() && $careRequest->status === 'pending' && !$careRequest->isFinalized()) {
            // Only allow selecting users who have initiated a chat for this care request
            $chatUserIds = Chat::where('care_request_id', $careRequest->id)->pluck('user_id');
            $users = User::whereIn('id', $chatUserIds)->orderBy('name')->get();
        }

        return view('care_requests.show', compact('careRequest', 'users'));
    }

    /**
     * Mark the care request as accepted by a chosen user.
     */
    public function accept(Request $request, CareRequest $careRequest)
    {
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'accepted_by' => 'required|exists:users,id|different:user_id',
        ], [
            'accepted_by.different' => 'No puedes seleccionarte a ti mismo como cuidador.',
        ]);

        // Enforce that the accepted user must have opened a chat for this care request
        $chatUserIds = Chat::where('care_request_id', $careRequest->id)->pluck('user_id')->toArray();
        if (!in_array($request->accepted_by, $chatUserIds)) {
            return back()->with('error', 'Solo puedes elegir a un cuidador que te haya contactado previamente por mensajes.');
        }

        if ($careRequest->isFinalized()) {
            return back()->with('error', 'No se puede aceptar una petición que ya ha finalizado.');
        }

        return redirect()->route('payments.checkout', [
            'care_request' => $careRequest->id,
            'caretaker_id' => $request->accepted_by,
        ]);
    }

    /**
     * Display a listing of finalized care requests.
     */
    public function history()
    {
        $today = now()->toDateString();

        // Finalized requests created by the user OR accepted by the user
        $finalizedRequests = CareRequest::where(function($query) {
                $query->where('user_id', auth()->id())
                      ->orWhere('accepted_by', auth()->id());
            })
            ->where(function($query) use ($today) {
                $query->where('end_date', '<', $today)
                      ->orWhere('status', 'finalized');
            })
            ->with(['dogs', 'user', 'acceptedBy'])
            ->latest()
            ->get();

        return view('care_requests.history', compact('finalizedRequests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CareRequest $careRequest)
    {
        // Ensure the user owns this request
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $careRequest->delete();

        return redirect()->route('care-requests.index')->with('success', 'Petición eliminada correctamente.');
    }

    /**
     * Display a listing of care requests favorited by the user.
     */
    public function favorites()
    {
        $favorites = auth()->user()->favoriteCareRequests()
            ->where('status', 'pending')
            ->where('end_date', '>=', now()->toDateString())
            ->with(['dogs', 'user'])
            ->latest()
            ->get();

        return view('care_requests.favorites', compact('favorites'));
    }

    /**
     * Toggle the favorite status of a care request.
     */
    public function toggleFavorite(CareRequest $careRequest)
    {
        auth()->user()->favoriteCareRequests()->toggle($careRequest->id);

        $isFavorited = auth()->user()->favoriteCareRequests()->where('care_request_id', $careRequest->id)->exists();
        $message = $isFavorited ? 'Petición añadida a tus favoritos ❤️' : 'Petición eliminada de tus favoritos 🤍';

        return back()->with('success', $message);
    }
}
