<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController;

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

// Restricted routes that require authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('news', [NewsController::class, 'store']); // Create
    Route::put('news/{id}', [NewsController::class, 'update']); // Update
    Route::delete('news/{id}', [NewsController::class, 'destroy']); // Delete
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Public routes
Route::get('news', [NewsController::class, 'index']); // Index
Route::get('news/{id}', [NewsController::class, 'show']); // Show
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
