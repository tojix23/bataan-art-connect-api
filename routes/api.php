<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfilePhotoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AccountController::class, 'register']);
Route::post('/login', [AccountController::class, 'login']);

Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::post('/photo', [ProfilePhotoController::class, 'upload']);
});

Route::prefix('list')->middleware('auth:sanctum')->group(function () {
    Route::get('/pending-users', [AccountController::class, 'pending_users']);
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::post('/verify-users', [AccountController::class, 'verify']);
});
