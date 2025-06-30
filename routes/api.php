<?php

use App\Http\Controller\Article\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\User\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(PasswordResetController::class)->prefix('user')->group(function () {
    Route::post('/password-reset', 'sendResetLinkEmail');
    Route::post('/password-reset/confirm', 'reset');
});

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/', 'me');
    });
});

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('preferences')->group(function () {
    Route::post('/', [UserPreferenceController::class, 'store']);
    Route::get('/', [UserPreferenceController::class, 'show']);
    Route::get('/feed', [UserPreferenceController::class, 'personalizedFeed']);
});

/*no use
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/