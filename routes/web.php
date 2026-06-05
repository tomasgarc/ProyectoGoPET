<?php

use App\Http\Controllers\CareRequestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Models\CareRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Sindicación de Contenidos: Canal RSS XML para Peticiones de Cuidado
Route::get('/feed/care-requests.xml', function () {
    $requests = CareRequest::where('status', 'pending')
        ->where('end_date', '>=', now()->toDateString())
        ->with(['dogs', 'user'])
        ->latest()
        ->get();

    $feed = view('feeds.care_requests_rss', compact('requests'));

    return response($feed, 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
    ]);
})->name('feeds.care-requests');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/dashboard/update-analytics', [DashboardController::class, 'updateAnalytics'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.update-analytics');

Route::middleware('auth')->group(function () {
    Route::resource('dogs', DogController::class);
    Route::get('/explore-requests', [CareRequestController::class, 'explore'])->name('care-requests.explore');
    Route::get('/care-requests/history', [CareRequestController::class, 'history'])->name('care-requests.history');
    Route::get('/care-requests/favorites', [CareRequestController::class, 'favorites'])->name('care-requests.favorites');
    Route::post('/care-requests/{care_request}/favorite', [CareRequestController::class, 'toggleFavorite'])->name('care-requests.favorite');
    Route::post('/care-requests/{care_request}/accept', [CareRequestController::class, 'accept'])->name('care-requests.accept');
    Route::resource('care-requests', CareRequestController::class);

    // Chats & Messaging
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::post('/care-requests/{care_request}/chat', [ChatController::class, 'start'])->name('chats.start');
    Route::post('/chats/{chat}/messages', [ChatController::class, 'storeMessage'])->name('chats.messages.store');

    // Payments & Escrow System
    Route::get('/care-requests/{care_request}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/care-requests/{care_request}/pay', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::get('/care-requests/{care_request}/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::post('/care-requests/{care_request}/release-payment', [PaymentController::class, 'releasePayment'])->name('payments.release');
    Route::post('/care-requests/{care_request}/refund-payment', [PaymentController::class, 'cancelAndRefund'])->name('payments.refund');
    Route::get('/wallet', [PaymentController::class, 'wallet'])->name('payments.wallet');

    // Reviews & Ratings
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/care-requests/{care_request}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stripe Webhook (No CSRF, outside auth middleware)
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->name('stripe.webhook');

require __DIR__.'/auth.php';
