<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agency\AgencyController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Role\PermissionsController;
use App\Http\Controllers\Role\RolesController;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });
//});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
//Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//Route::middleware(['auth:sanctum'])->group(function () {
//    Route::controller(PermissionsController::class)->group(function () {
//        Route::post('/permissions', 'store');            // Create permission
//        Route::get('/permissions', 'index');             // List permissions
//        Route::put('/permissions/{id}', 'update');       // Update permission
//        Route::delete('/permissions/{id}', 'destroy');   // Delete permission
//    });
//});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(PermissionsController::class)->group(function () {
        Route::post('/permissions', 'store');            // Create permission

        Route::put('/permissions/{id}', 'update');       // Update permission
        Route::delete('/permissions/{id}', 'destroy');   // Delete permission
    });
});
Route::get('/permissions', [PermissionsController::class,'index']);             // List permissions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getUserInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update/{ulid}', [AuthController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/roles', [RolesController::class, 'store']);

});
Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'index']);
    Route::post('/admin/users', [UserController::class, 'store']);
});
Route::prefix('agencies')->group(function () {
    Route::get('/', [AgencyController::class, 'index']);
    Route::post('/', [AgencyController::class, 'store']);
    //    Route::get('/{id}', [AgencyController::class, 'show']);
    //    Route::put('/{id}', [AgencyController::class, 'update']);
    //    Route::delete('/{id}', [AgencyController::class, 'destroy']);
    //    Route::get('/search', [AgencyController::class, 'search']);
});
Route::middleware('auth:sanctum')->put('/user/{ulid}', [AuthController::class, 'update']);

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUserInfo']);
