<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users')
    ->controller(UserController::class)
    ->group(function () {
        Route::post('', 'store')->name('store');
        Route::get('{user}', 'show')->name('show');
    });
