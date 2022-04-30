<?php

use App\Http\Controllers\Action\ActionController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\User\UserController;
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

Route::get('/', fn() => 'Maker-Checker API');

Route::group([], function () {
    Route::post('register', [RegisterController::class, 'store'])
        ->name('register')
        ->middleware('throttle:authentication');
    Route::post('login', [LoginController::class, 'store'])
        ->name('login')
        ->middleware('throttle:authentication');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('actions', ActionController::class)->except(['store']);
    Route::apiResource('users', UserController::class);
});
