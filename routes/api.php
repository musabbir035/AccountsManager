<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/customers', [CustomerController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/store', [UserController::class, 'store']);
    Route::put('/users/update/{id}', [UserController::class, 'update']);
    Route::get('/users/update-status/{id}', [UserController::class, 'updateStatus']);
    Route::get('/users/delete/{id}', [UserController::class, 'destroy']);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);

    Route::post('/customers', [CustomerController::class, 'index']);
    //    Route::get('/customer', [CustomerController::class, 'show']);
    Route::post('/customers/store', [CustomerController::class, 'store']);
    Route::put('/customers/update/{id}', [CustomerController::class, 'update']);
    Route::get('/customers/delete/{id}', [CustomerController::class, 'destroy']);

    Route::get('/transactions/{id?}', [TransactionController::class, 'index']);
    Route::post('/transactions/store', [TransactionController::class, 'store']);
    Route::post('/generate-ledger', [TransactionController::class, 'generateLedgerPdf']);
});
Route::get('/customer', [CustomerController::class, 'show']);
