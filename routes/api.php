<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\BdmHospitalController;
use App\Http\Controllers\BlogNewsController;
use App\Http\Controllers\customizeRoleController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MissionVisionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\WishersController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->middleware(['permissions:profile_all'])->group(function () {
        Route::get('/profile', [AuthUserController::class, 'userProfile'])->name('user.userProfile');
        Route::put('/profile', [AuthUserController::class, 'updateUserProfile'])->name('user.updateProfile');
        Route::post('/change-password', [AuthUserController::class, 'changePassword'])->name('user.changePassword');
    });

    //->middleware(['roles:guest,admin', 'permissions:profile_view,view_users']);
    Route::prefix('web')->group(function () {
        // Permission-related routes
        Route::middleware(['permissions:permission_all'])->group(function () {
            Route::post('/assign-role-has-permissions', [AuthUserController::class, 'assignPermissionsToRole']);
            Route::post('/remove-role-has-permissions', [AuthUserController::class, 'removeRoleHasPermission']);
            Route::post('/create-permissions', [AuthUserController::class, 'createPermission']);
            Route::post('/permissions', [CustomizeRoleController::class, 'storePermission']);
            Route::get('/permissions-all', [CustomizeRoleController::class, 'permissionsAll']);
        });
        // Role-related routes
        Route::middleware(['permissions:role_all'])->group(function () {
            Route::resource('/roles', CustomizeRoleController::class);
        });
        Route::resource('/video-galleries', GalleryController::class)->middleware(['permissions:galleries-all']);
        Route::resource('/sliders', SliderController::class)->middleware(['permissions:sliders_all']);
        Route::resource('/notices', controller: NoticeController::class)->middleware(['permissions:notice-all']);
        Route::resource('/wishers', WishersController::class)->middleware(['permissions:wisher-all']);
        Route::resource('/blog-news', BlogNewsController::class)->middleware(['permissions:blogNews-all']);
        Route::resource('/doctor-profile', DoctorProfileController::class)->middleware(['permissions:doctor-all']);
        Route::resource('/services', ServiceController::class)->middleware(['permissions:services-all']);
        Route::resource('/mission-vision', controller: MissionVisionController::class)->middleware(['permissions:mission-vision-all']);
        Route::resource('/bdm-hospital-about', controller: BdmHospitalController::class)->middleware(['permissions:bdm-all']);
        Route::resource('/settings', controller: SettingController::class)->middleware(['permissions:settings-all']);
    });
});

Route::prefix('public')->group(function () {
    Route::get('/sliders', [PublicController::class, 'slider']);
    Route::get('/notices', [PublicController::class, 'noticeData']);
    Route::get('/video-gallery', [PublicController::class, 'videoGallery']);
    Route::get('/wishers', [PublicController::class, 'wishers']);

    Route::get('/blog-news', [PublicController::class, 'blogNews']);
    Route::get('/blog-news/{id}', [PublicController::class, 'blogNewsDetails']);

    Route::get('/doctor-profile', [PublicController::class, 'doctorProfile']);
    Route::get('/doctor-profile-details/{id}', [PublicController::class, 'doctorProfileDetails']);

    Route::get('/services', [PublicController::class, 'services']);
    Route::get('/services-details/{id}', [PublicController::class, 'servicesDetails']);

    Route::get('/mission-vision', [PublicController::class, 'missionVision']);
    Route::get('/bdm-hospital-about', [PublicController::class, 'bmsAbout']);
    Route::get('/settings/{id}', [SettingController::class, 'show']);
    Route::get('/bdm-hospital-about/{id}', [BdmHospitalController::class, 'show']);
});
