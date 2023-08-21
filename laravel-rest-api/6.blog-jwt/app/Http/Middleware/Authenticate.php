<?php

namespace App\Http\Middleware;

use Exception;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use JWTAuth;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   */
  protected function redirectTo(Request $request): ?string
  {
    try {
      JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json(['success' => false, 'message' => 'Token is Invalid'], 401);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json(['success' => false, 'message' => 'Token is Expired'], 401);
      } {
        return response()->json(['success' => false, 'message' => 'Authorization Token not found'], 401);
      }
    }
    return $request->expectsJson() ? null : route('login');
  }
}
