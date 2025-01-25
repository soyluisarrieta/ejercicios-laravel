<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    function login(LoginRequest $request) {
      // Login user
      if (!Auth::attempt($request->only('email','password'))) {
        Helper::sendError('El correo electrónico o la contraseña son incorrectos.');
      }
      
      // Send response
      return new UserResource(auth()->user());
    }
}
