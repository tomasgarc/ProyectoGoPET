<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('dogs', \App\Http\Controllers\DogController::class);
    Route::get('/explore-requests', [\App\Http\Controllers\CareRequestController::class, 'explore'])->name('care-requests.explore');
    Route::get('/care-requests/history', [\App\Http\Controllers\CareRequestController::class, 'history'])->name('care-requests.history');
    Route::post('/care-requests/{care_request}/accept', [\App\Http\Controllers\CareRequestController::class, 'accept'])->name('care-requests.accept');
    Route::resource('care-requests', \App\Http\Controllers\CareRequestController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
