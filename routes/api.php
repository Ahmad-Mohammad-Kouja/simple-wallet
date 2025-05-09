<?php

use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function () {
        Route::post('', 'store')->name('store');
        Route::get('{user}', 'show')->name('show');
    });

Route::prefix('wallets')
    ->name('wallets.')
    ->controller(WalletController::class)
    ->group(function () {
        Route::post('deposit', 'deposit')->name('deposit');
        Route::post('withdraw', 'withdraw')->name('withdraw');
        Route::post('transfer', 'transfer')->name('transfer');
    });

Route::prefix('transactions')
    ->name('transactions.')
    ->controller(TransactionController::class)
    ->group(function () {
        Route::get('', 'index')->name('index');
    });
