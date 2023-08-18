<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
  function register(Request $request)
  {
    return User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => Hash::make($request->input('password')),
    ]);
  }

  function login(Request $request)
  {
    if (!Auth::attempt($request->only('email', 'password'))) {
      return response([
        'message' => 'Invalid credentials!'
      ], Response::HTTP_UNAUTHORIZED);
    }

    /** @var User $user */
    $user = Auth::user();
    $token = $user->createToken('token')->plainTextToken;

    $cookie = cookie('jwt', $token, 60 * 24); // 1 day

    return response([
      'message' => 'Success'
    ])->withCookie($cookie);
  }

  function user()
  {
    return 'Authenticated user';
  }
}
