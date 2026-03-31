<?php

use App\Http\Controllers\MpesaWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ─── M-Pesa Webhook ──────────────────────────────────────────────────────────
// This endpoint must be publicly accessible (no auth middleware).
// Safaricom POSTs payment callbacks here after STK Push completion.
Route::prefix('mpesa')->group(function () {
    Route::post('/callback', [MpesaWebhookController::class, 'callback'])
        ->name('mpesa.callback')
        ->withoutMiddleware(['auth:sanctum']);
});

