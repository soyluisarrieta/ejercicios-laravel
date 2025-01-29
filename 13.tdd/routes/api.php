<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Auth\UpdateProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', function (Request $request) {
    return response()->json(['msg' => 'Hello World!']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::put('/profile', [UpdateProfileController::class, 'update']);
Route::put('/password', [UpdatePasswordController::class, 'update']);
Route::post('/reset-password', [ResetPasswordController::class, 'send']);
Route::put('/reset-password', [ResetPasswordController::class, 'resetPassword']);
