<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('portal-sdmuh', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('portal-sdmuh', [AuthenticatedSessionController::class, 'store']);

    Route::get('portal-sdmuh/lupa-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('portal-sdmuh/lupa-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');
    Route::get('portal-sdmuh/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('portal-sdmuh/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
