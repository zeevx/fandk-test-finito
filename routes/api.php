<?php

use Illuminate\Http\Request;
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

//Register User
Route::post('/register', [App\Http\Controllers\ApiController::class, 'register']);

//Credit User Wallet After Successful Payment
Route::post('/wallet/credit',[App\Http\Controllers\ApiController::class, 'credit'])->middleware('auth:api');

//User transfer to another user
Route::post('/wallet/transfer',[App\Http\Controllers\ApiController::class, 'transfer'])->middleware('auth:api');

