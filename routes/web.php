<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

Route::fallback(function () {
    return view('index');
});

Route::prefix('api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'store']);
        Route::post('login', [AuthController::class, 'authenticate']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('whoami', [AuthController::class, 'whoami']);
    });

    Route::prefix('user')->group(function () {
        Route::get('', [UserController::class, 'index']);
    });

    Route::prefix('transaction')->group(function () {
        Route::get('', [TransactionController::class, 'get']);
        Route::post('', [TransactionController::class, 'store']);
        Route::delete('{id}', [TransactionController::class, 'delete']);
        Route::put('{id}', [TransactionController::class, 'update']);
    });

    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{id}', [ClientController::class, 'getClient']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);
    Route::put('clients/{id}', [ClientController::class, 'update']);

    Route::fallback(function () {
        return view('index');
    });
});
