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
    return view('welcome');
});

// define route to access invoice document and require admin authentication
Route::group(['middleware' => ['auth']], function () {
    Route::get('preview/invoice/{invoice?}',[DocumentsController::class,'previewInvoice'])
        ->name('preview.invoice');
    Route::get('preview/credit-note/{creditNote?}',[DocumentsController::class,'previewCreditNote'])
        ->name('preview.credit-note');
});
