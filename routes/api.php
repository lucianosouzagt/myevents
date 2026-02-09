<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CheckinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Events
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Public RSVP & QR Code
Route::get('/invitations/{token}', [InvitationController::class, 'show']);
Route::post('/invitations/{token}/rsvp', [InvitationController::class, 'rsvp']);
Route::get('/invitations/{token}/qrcode', [CheckinController::class, 'showQrCode']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    
    // Invitations
    Route::post('/invitations', [InvitationController::class, 'store']);

    // Check-in (Organizer)
    Route::post('/checkin', [CheckinController::class, 'store']);
});
