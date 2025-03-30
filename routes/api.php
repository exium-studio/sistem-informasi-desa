<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Public\PublicRequestController;
use App\Http\Controllers\Web\Dashboard\AnnouncementController;
use App\Http\Controllers\Web\Dashboard\FacilityController;
use App\Http\Controllers\Web\Dashboard\FundMutationController;
use App\Http\Controllers\Web\Dashboard\InventoryController;
use App\Http\Controllers\Web\Dashboard\OfficialContactController;
use App\Http\Controllers\Web\Dashboard\PopulationController;
use App\Http\Controllers\Web\Dashboard\VillageIndexController;
use App\Http\Controllers\Web\InboxController;
use App\Http\Controllers\Web\MasterData\BloodTypeController;
use App\Http\Controllers\Web\MasterData\CitizenshipController;
use App\Http\Controllers\Web\MasterData\DocumentTypeController;
use App\Http\Controllers\Web\MasterData\EducationController;
use App\Http\Controllers\Web\MasterData\ExpenseCategoryController;
use App\Http\Controllers\Web\MasterData\FacilityController as MasterDataFacilityController;
use App\Http\Controllers\Web\MasterData\IncomeSourceController;
use App\Http\Controllers\Web\MasterData\InventoryController as MasterDataInventoryController;
use App\Http\Controllers\Web\MasterData\JobTypeController;
use App\Http\Controllers\Web\MasterData\MarriedStatusController;
use App\Http\Controllers\Web\MasterData\RelationshipStatusController;
use App\Http\Controllers\Web\MasterData\ReligionController;
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

	Route::group(['prefix' => 'public-request', 'middleware' => ['verified.role:public']], function () {
		Route::get('/get-religion', [PublicRequestController::class, 'getReligion']);
		Route::get('/get-education', [PublicRequestController::class, 'getEducation']);
		Route::get('/get-blood-type', [PublicRequestController::class, 'getBloodType']);
		Route::get('/get-job-type', [PublicRequestController::class, 'getJobType']);
		Route::get('/get-relationship-status', [PublicRequestController::class, 'getRelationshipStatus']);
		Route::get('/get-married-status', [PublicRequestController::class, 'getMariedStatus']);
		Route::get('/get-citizenship', [PublicRequestController::class, 'getCitizenship']);
		Route::get('/get-income-source', [PublicRequestController::class, 'getIncomeSource']);
		Route::get('/get-expense-category', [PublicRequestController::class, 'getExpenseCategory']);
		Route::get('/get-document-type', [PublicRequestController::class, 'getDocumentType']);
		Route::get('/get-facility', [PublicRequestController::class, 'getFacility']);
		Route::get('/get-inventory', [PublicRequestController::class, 'getInventory']);
	});

	Route::group(['prefix' => 'web', 'middleware' => ['verified.role:web']], function () {
		Route::group(['prefix' => 'sid'], function () {
			Route::get('/inbox', [InboxController::class, 'index']);
			Route::get('/inbox-unread', [InboxController::class, 'unreadCount']);
			Route::post('/inbox-update', [InboxController::class, 'update']);

			// TODO: Test semua master-data
			Route::group(['prefix' => 'dashboard'], function () {
				Route::get('/population', [PopulationController::class, 'index']);
				Route::post('/population-growth', [PopulationController::class, 'growthPerYear']);
				Route::post('/current-population', [PopulationController::class, 'growthSummary']);
				Route::get('/facility', [FacilityController::class, 'index']);
				Route::get('/inventory', [InventoryController::class, 'index']);
				Route::apiResource('/announcement', AnnouncementController::class);
				Route::apiResource('/official-contact', OfficialContactController::class);
				Route::get('/village', [VillageIndexController::class, 'index']);
				Route::post('/fund-mutation', [FundMutationController::class, 'index']);
				Route::post('/fund-mutation-income', [FundMutationController::class, 'getIncomePerSource']);
				Route::post('/fund-mutation-expense', [FundMutationController::class, 'getExpensePerCategory']);
			});

			Route::group(['prefix' => 'master-data'], function () {
				Route::apiResource('/religion', ReligionController::class);
				Route::post('/religion/{id}/restore', [ReligionController::class, 'restore']);

				Route::apiResource('/education', EducationController::class);
				Route::post('/education/{id}/restore', [EducationController::class, 'restore']);

				Route::apiResource('/blood-type', BloodTypeController::class);
				Route::post('/blood-type/{id}/restore', [BloodTypeController::class, 'restore']);

				Route::apiResource('/job-type', JobTypeController::class);
				Route::post('/job-type/{id}/restore', [JobTypeController::class, 'restore']);

				Route::apiResource('/relationship-status', RelationshipStatusController::class);
				Route::post('/relationship-status/{id}/restore', [RelationshipStatusController::class, 'restore']);

				Route::apiResource('/married-status', MarriedStatusController::class);
				Route::post('/married-status/{id}/restore', [MarriedStatusController::class, 'restore']);

				Route::apiResource('/citizenship', CitizenshipController::class);
				Route::post('/citizenship/{id}/restore', [CitizenshipController::class, 'restore']);

				Route::apiResource('/income-source', IncomeSourceController::class);
				Route::post('/income-source/{id}/restore', [IncomeSourceController::class, 'restore']);

				Route::apiResource('/expense-category', ExpenseCategoryController::class);
				Route::post('/expense-category/{id}/restore', [ExpenseCategoryController::class, 'restore']);

				Route::apiResource('/document-type', DocumentTypeController::class);
				Route::post('/document-type/{id}/restore', [DocumentTypeController::class, 'restore']);

				Route::apiResource('/facility', MasterDataFacilityController::class);
				Route::post('/facility/{id}/restore', [MasterDataFacilityController::class, 'restore']);

				Route::apiResource('/inventory', MasterDataInventoryController::class);
				Route::post('/inventory/{id}/restore', [MasterDataInventoryController::class, 'restore']);
			});
		});
	});

	Route::group(['prefix' => 'mobile', 'middleware' => ['verified.role:mobile']], function () {
		Route::group(['prefix' => 'sid/home'], function () {});
	});
});
