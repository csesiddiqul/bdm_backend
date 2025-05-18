<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\customizeRoleController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SliderController;
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
    });
});

Route::prefix('public')->group(function () {
    Route::get('sliders', [PublicController::class, 'slider']);
});
