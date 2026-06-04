<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the user's reviews.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Reviews received as caretaker (where the reviewer was the owner)
        $receivedAsCaretaker = $user->receivedReviews()
            ->whereHas('careRequest', function ($query) use ($user) {
                $query->where('accepted_by', $user->id);
            })
            ->with(['reviewer', 'careRequest.dogs'])
            ->latest()
            ->get();

        // 2. Reviews received as owner (where the reviewer was the caretaker)
        $receivedAsOwner = $user->receivedReviews()
            ->whereHas('careRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['reviewer', 'careRequest.dogs'])
            ->latest()
            ->get();

        // 3. Reviews written by me
        $writtenReviews = $user->reviews()
            ->with(['reviewee', 'careRequest.dogs'])
            ->latest()
            ->get();

        // 4. Calculate star distribution (for received reviews)
        $starsDistribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        $allReceivedReviews = $user->receivedReviews;
        $totalReceivedCount = $allReceivedReviews->count();

        foreach ($allReceivedReviews as $rev) {
            $r = (int) $rev->rating;
            if (isset($starsDistribution[$r])) {
                $starsDistribution[$r]++;
            }
        }

        // 5. Finalized care requests involving this user where they haven't submitted a review yet
        $pendingToReview = CareRequest::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('accepted_by', $user->id);
        })
            ->where('status', 'finalized')
            ->whereDoesntHave('reviews', function ($query) use ($user) {
                $query->where('reviewer_id', $user->id);
            })
            ->with(['dogs', 'user', 'acceptedBy'])
            ->latest()
            ->get();

        return view('reviews.index', compact(
            'receivedAsCaretaker',
            'receivedAsOwner',
            'writtenReviews',
            'starsDistribution',
            'totalReceivedCount',
            'pendingToReview'
        ));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, CareRequest $careRequest)
    {
        $user = auth()->user();

        if (! $careRequest->canBeReviewedBy($user)) {
            abort(403, 'No tienes autorización para valorar este servicio o ya lo has valorado.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ], [
            'rating.required' => 'Debes seleccionar una puntuación de estrellas.',
            'rating.integer' => 'Puntuación no válida.',
            'rating.min' => 'La puntuación mínima es 1 estrella.',
            'rating.max' => 'La puntuación máxima es 5 estrellas.',
            'comment.required' => 'Debes escribir un comentario sobre tu experiencia.',
            'comment.min' => 'El comentario debe tener al menos 5 caracteres.',
            'comment.max' => 'El comentario no puede exceder los 1000 caracteres.',
        ]);

        // Identify who is being reviewed
        if ($careRequest->user_id === $user->id) {
            // Owner is reviewing the caretaker
            $revieweeId = $careRequest->accepted_by;
        } else {
            // Caretaker is reviewing the owner
            $revieweeId = $careRequest->user_id;
        }

        if (! $revieweeId) {
            return back()->with('error', 'No se puede reseñar un servicio que no tiene un cuidador asignado.');
        }

        Review::create([
            'care_request_id' => $careRequest->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', '¡Muchas gracias! Tu reseña ha sido registrada con éxito.');
    }
}
