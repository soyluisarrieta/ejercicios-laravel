<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UpdatePasswordController;
use App\Http\Controllers\UpdateProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', function (Request $request) {
    return response()->json(['msg' => 'Hello World!']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::put('/profile', [UpdateProfileController::class, 'update']);
Route::put('/password', [UpdatePasswordController::class, 'update']);
