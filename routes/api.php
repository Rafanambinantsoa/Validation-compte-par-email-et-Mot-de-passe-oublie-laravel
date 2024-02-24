<?php

use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", [UserController::class , "registration"] );
Route::post("/login", [UserController::class , "login"] );
Route::post("/validate/{user}", [UserController::class , "valideUncompte"] );
Route::post("/forgot-password", [UserController::class , "forgotPassword"] );
Route::post("/reset-password", [UserController::class , "resetPassword"] );
Route::post("/password.reset/{id}", [UserController::class , "check"] );
