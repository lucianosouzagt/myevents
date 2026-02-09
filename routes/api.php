<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Events
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Public RSVP
Route::get('/invitations/{token}', [InvitationController::class, 'show']);
Route::post('/invitations/{token}/rsvp', [InvitationController::class, 'rsvp']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    
    // Invitations
    Route::post('/invitations', [InvitationController::class, 'store']);
});
