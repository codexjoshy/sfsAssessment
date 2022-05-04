<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to eventos API', 'status' => 'Connected']);
});
Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1/'], function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('user', UserController::class)->only(['index', 'update', 'uploadImage']);
    Route::post('user/{user}/upload', [UserController::class, 'uploadImage']);
});