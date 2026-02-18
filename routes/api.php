<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CheckinController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\MailTestController;
use App\Http\Controllers\Api\JwtAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/jwt/login', [JwtAuthController::class, 'login']);

// Public Events
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Public RSVP & QR Code
Route::get('/invitations/{token}', [InvitationController::class, 'show']);
Route::post('/invitations/{token}/rsvp', [InvitationController::class, 'rsvp']);
Route::get('/invitations/{token}/qrcode', [CheckinController::class, 'showQrCode']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    
    // Invitations
    Route::post('/invitations', [InvitationController::class, 'store']);

    // Check-in (Organizer)
    Route::post('/checkin', [CheckinController::class, 'store']);
});

// Mail Test endpoint (controle de habilitação no controller)
Route::post('/mail/test', [MailTestController::class, 'send'])->middleware('throttle:5,1');

// JWT-protected sample (role-based)
Route::middleware('jwt.auth:admin')->get('/admin/ping', function () {
    return ['ok' => true, 'role' => 'admin'];
});
