<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ArtistController;
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
    Route::post('/account_update', [AccountController::class, 'enable_or_disable_acc']);
    Route::get('/accounts', [AccountController::class, 'registered_account']);
    Route::post('/cancel-verify', [AccountController::class, 'cancel_verify']);
});

Route::prefix('post')->middleware('auth:sanctum')->group(function () {
    Route::post('/create-post', [PostController::class, 'create_post']);
    Route::post('/display-post', [PostController::class, 'verified_post']);
    Route::post('/add-comment', [CommentController::class, 'add_comment']);
    Route::post('/comments', [CommentController::class, 'list']);
    Route::get('/artists-post', [PostController::class, 'for_verification_post']);
    Route::post('/approve-post', [PostController::class, 'approve_post']);
    Route::post('/cancel-post', [PostController::class, 'cancel_post']);
});

Route::prefix('artist')->middleware('auth:sanctum')->group(function () {
    Route::get('/registered', [AccountController::class, 'registered_account']);
    Route::post('/artist-list-by-type', [ArtistController::class, 'artist_list']);
});
