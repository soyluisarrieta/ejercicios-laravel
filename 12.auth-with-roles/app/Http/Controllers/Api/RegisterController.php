<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
  function register(RegisterRequest $request) {
    // Register user
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
    ]);

    // Assign role
    $user_role = Role::where(['name' => 'user'])->first();
    if ($user_role) {
      $user->assignRole($user_role);
    }
    
    // Send response
    return new UserResource($user);
  }
}
