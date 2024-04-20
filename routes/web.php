<?php

use App\Http\Controllers\DocumentsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // redirect to the admin route
    return redirect()
        ->route('filament.admin.pages.dashboard');
});

// define route to access invoice document and require admin authentication
Route::group(['middleware' => ['auth']], function () {
    Route::get('preview/invoice/{invoice?}',[DocumentsController::class,'previewInvoice'])
        ->name('preview.invoice');
    Route::get('preview/manual-invoice/{invoice?}',[DocumentsController::class,'previewManualInvoice'])
        ->name('preview.manual-invoice');
    Route::get('preview/credit-note/{creditNote?}',[DocumentsController::class,'previewCreditNote'])
        ->name('preview.credit-note');
    Route::get('preview/receipt/{receipt?}',[DocumentsController::class,'previewReceipt'])
        ->name('preview.receipt');
});
