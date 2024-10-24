<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
//use App\Http\Controllers\PermissionsController as ControllersPermissionsController;

use App\Http\Controllers\Role\RolesController;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });
//});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getUserInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update/{ulid}', [AuthController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/roles', [RolesController::class, 'store']);

});
Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::post('/admin/users', [UserController::class, 'store']);

});
Route::middleware('auth:sanctum')->put('/user/{ulid}', [AuthController::class, 'update']);

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUserInfo']);
