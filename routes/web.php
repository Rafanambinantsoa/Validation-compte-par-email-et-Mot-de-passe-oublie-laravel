<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get("/auth/google", [UserController::class , "loginGoogleForm"] );
Route::get("auth/google/login", [UserController::class , "handleGoogleLogin"] );
Route::get("authGoogle/CallBack", [UserController::class , "googleCallback"] );
Route::get("auth/git/login", [UserController::class , "gitHandleLogin"] );
Route::get("/auth/authGithub/CallBack", [UserController::class , "gitCallback"] );