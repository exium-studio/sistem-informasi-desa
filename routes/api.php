<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Web\Dashboard\OfficialContactController;
use App\Http\Controllers\Web\Dashboard\PengumumanController;
use App\Http\Controllers\Web\Dashboard\PopulasiController;
use Illuminate\Support\Facades\Route;

// Login Section
Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['custom.throttle:5,1'])->group(function () {
	Route::post('/send-otp', [ForgotPasswordController::class, 'sendOTP']);
	Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOTP']);
	Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'custom.throttle:60,1'])->group(function () {
	Route::get('/logout', [LoginController::class, 'logout'])->middleware('web');
	Route::get('/user-info', [LoginController::class, 'getUserInfo']);

	Route::group(['prefix' => 'web', 'middleware' => ['verified.role']], function () {
		Route::group(['prefix' => 'sid/dashboard'], function () {
			Route::get('/population', [PopulasiController::class, 'populasi']);
			Route::apiResource('/announcement', PengumumanController::class);
			Route::apiResource('/official-contact', OfficialContactController::class);
		});
	});

	Route::group(['prefix' => 'mobile', 'middleware' => ['verified.role']], function () {
		Route::group(['prefix' => 'sid/home'], function () {
			
		});
	});
});
