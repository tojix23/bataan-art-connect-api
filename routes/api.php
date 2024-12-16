<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\PersonalInfoController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ArtistPackageController;
use App\Http\Controllers\LikePostController;
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
Route::post('send-password-reset', [PasswordResetController::class, 'sendPasswordResetEmail']);
Route::post('register-email', [AccountController::class, 'email_registration']);
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
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
    Route::post('/update-rate', [ArtistController::class, 'update_service_rate']);
    Route::post('/update-bio', [PersonalInfoController::class, 'update_bio']);
    Route::post('/change-pass', [AccountController::class, 'change_password']);
});

Route::prefix('post')->middleware('auth:sanctum')->group(function () {
    Route::post('/create-post', [PostController::class, 'create_post']);
    Route::post('/display-post', [PostController::class, 'verified_post']);
    Route::post('/get-my-post', [PostController::class, 'get_my_post']);
    Route::post('/display-search-post', [PostController::class, 'display_post_by_search_artist']);
    Route::post('/add-comment', [CommentController::class, 'add_comment']);
    Route::post('/comments', [CommentController::class, 'list']);
    Route::get('/artists-post', [PostController::class, 'for_verification_post']);
    Route::post('/approve-post', [PostController::class, 'approve_post']);
    Route::post('/cancel-post', [PostController::class, 'cancel_post']);
    Route::post('/update-post', [PostController::class, 'update_post']);
    Route::post('/delete-post', [PostController::class, 'delete_post']);
    Route::post('/like-post', [LikePostController::class, 'like_post']);
});

Route::prefix('artist')->middleware('auth:sanctum')->group(function () {
    Route::get('/registered', [AccountController::class, 'registered_account']);
    Route::post('/artist-list-by-type', [ArtistController::class, 'artist_list']);
    Route::post('/get-rate', [ArtistController::class, 'get_rate']);
    Route::post('/artist-by-id', [ArtistController::class, 'get_artist_by_id']);
});

Route::prefix('message')->middleware('auth:sanctum')->group(function () {
    Route::post('/send', [MessageController::class, 'send_a_message']);
    Route::post('/get-message', [MessageController::class, 'get_my_message']);
    Route::post('/reply-message', [MessageController::class, 'reply_a_message']);
});

Route::prefix('connection')->middleware('auth:sanctum')->group(function () {
    Route::post('/send-request', [ConnectionController::class, 'send_connection']);
    Route::post('/connection-status-client', [ConnectionController::class, 'connection_status_client']);
    Route::post('/connection-status-artist-client', [ConnectionController::class, 'connection_status_artist_and_client']);
    Route::post('/connection-status-artist', [ConnectionController::class, 'connection_status_artist']);
    Route::post('/approve-connection', [ConnectionController::class, 'approve_connection']);
    Route::post('/reject-connection', [ConnectionController::class, 'reject_connection']);
});

Route::prefix('task')->middleware('auth:sanctum')->group(function () {
    Route::post('/send-task', [RatingController::class, 'send_task']);
    Route::post('/get-task-by-client', [RatingController::class, 'get_task_by_client']);
    Route::post('/get-task-by-artist', [RatingController::class, 'get_task_by_artist']);
    Route::post('/cancel-task-by-id', [RatingController::class, 'cancel_task_by_id']);
    Route::post('/get-task-for-confirm-in-artist', [RatingController::class, 'get_task_by_artist_for_confirmation']);
    Route::post('/get-task-for-confirmed-in-artist', [RatingController::class, 'get_task_by_artist_confirmed']);
    Route::post('/confirm-task', [RatingController::class, 'confirm_task_by_artist']);
    Route::post('/done-task', [RatingController::class, 'mark_as_done_by_artist']);
    Route::post('/rate-task', [RatingController::class, 'rate_task']);
    Route::post('/rate-artist', [RatingController::class, 'get_user_rating']);
    Route::post('/get-feedback', [RatingController::class, 'get_feedback']);
});

Route::prefix('package')->middleware('auth:sanctum')->group(function () {
    Route::post('/add-package', [ArtistPackageController::class, 'add_package']);
    Route::post('/get-package', [ArtistPackageController::class, 'package_list']);
    Route::post('/enable-or-disable-package', [ArtistPackageController::class, 'enable_disable_package']);
    Route::post('/get-package-booking', [ArtistPackageController::class, 'package_list_enabled']);
});
