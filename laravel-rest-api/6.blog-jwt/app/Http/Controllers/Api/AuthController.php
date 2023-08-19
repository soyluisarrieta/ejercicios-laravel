<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(LoginRequest $request)
  {
    $credentials = $request->validated();
    try {
      if (!$token = JWTAuth::attempt($credentials)) {
        return response([
          'success' => false,
          'message' => 'Invalid email or password, try again',
        ], 401);
      }

      $user = JWTAuth::user();
    } catch (JWTException $e) {
      return response([
        'success' => false,
        'message' => 'Technical error!'
      ], 500);
    }
    return $this->respondWithToken($token, $user, 'User login successfully!');
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    JWTAuth::parseToken()->invalidate();
    return response()->json([
      'success' => true,
      'message' => 'User logout successfully!'
    ]);
  }

  /**
   * Create new user
   * 
   * @return void
   */
  public function register(RegisterRequest $request)
  {
    $data = $request->validated();
    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => bcrypt($data['password'])
    ]);

    $token = JWTAuth::fromUser($user);
    return $this->respondWithToken($token, $user, 'User created successfully!');
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function profile()
  {
    $user = JWTAuth::parseToken()->authenticate();
    return response()->json([
      'success' => true,
      'message' => 'User data found successfully!',
      'data' => ['user' => $user]
    ]);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    $user = JWTAuth::parseToken()->authenticate();
    $newToken = JWTAuth::refresh();
    return $this->respondWithToken($newToken, $user, 'Token refresh successfully!');
  }



  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function respondWithToken($token, $user, $message)
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => [
        'user' => $user,
        'authorization' => [
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]
      ],
    ], 200);
  }
}
