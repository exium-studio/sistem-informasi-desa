<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Web\Dashboard\AnnouncementController;
use App\Http\Controllers\Web\Dashboard\FundMutationController;
use App\Http\Controllers\Web\Dashboard\OfficialContactController;
use App\Http\Controllers\Web\Dashboard\PopulationController;
use App\Http\Controllers\Web\Dashboard\VillageIndexController;
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

	Route::group(['prefix' => 'web', 'middleware' => ['verified.role:web']], function () {
		Route::group(['prefix' => 'sid/dashboard'], function () {
			Route::get('/population', [PopulationController::class, 'index']);
			Route::apiResource('/announcement', AnnouncementController::class);
			Route::apiResource('/official-contact', OfficialContactController::class);
			Route::get('/village', [VillageIndexController::class, 'index']);
			Route::post('/fund-mutation', [FundMutationController::class, 'index']);
		});
	});

	Route::group(['prefix' => 'mobile', 'middleware' => ['verified.role:mobile']], function () {
		Route::group(['prefix' => 'sid/home'], function () {
			
		});
	});
});
