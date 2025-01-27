<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', function (Request $request) {
    return response()->json(['msg' => 'Hello World!']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
