<?php

use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet', WalletController::class)->name('api.wallet.show');

    Route::post('/deposits', [DepositController::class, 'store'])->name('api.deposits.store');

    Route::post('/transfers', [TransferController::class, 'store'])->name('api.transfers.store');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
});
