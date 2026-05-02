<?php

use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet', WalletController::class)->name('api.wallet.show');

    Route::post('/deposits', [DepositController::class, 'store'])->name('api.deposits.store');
});
