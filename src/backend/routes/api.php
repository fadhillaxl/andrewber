<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'submit']);
Route::post('/login/verify', [LoginController::class, 'verify']);
Route::post('/trip_shared/{token}', [TripController::class, 'shared_trip']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/send-otp/{otp}', [WhatsAppController::class, 'sendOtp'])->name('send-otp');


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/driver', [DriverController::class, 'show']);
    Route::post('/driver', [DriverController::class, 'update']);

    Route::post('/trip', [TripController::class, 'store']);
    Route::get('/trip/{trip}', [TripController::class, 'show']);
    Route::post('/trip/{trip}/accept', [TripController::class, 'accept']);
    Route::post('/trip/{trip}/start', [TripController::class, 'start']);
    Route::post('/trip/{trip}/end', [TripController::class, 'end']);
    Route::post('/trip/{trip}/location', [TripController::class, 'location']);
    Route::post('/trip/{trip}/share', [TripController::class, 'share']);

    Route::get('/profile', [UserController::class, 'show']);

    Route::get('/user', function(Request $request) {
        return $request->user();
    });
});

