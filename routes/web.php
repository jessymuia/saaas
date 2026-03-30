<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tenancy-debug', function () {
    return response()->json([
        'is_central' => !tenancy()->initialized,
        'tenant_id' => tenancy()->tenant?->id ?? null,
        'central_domains' => config('tenancy.central_domains'),
        'current_host' => request()->getHost(),
    ]);
});