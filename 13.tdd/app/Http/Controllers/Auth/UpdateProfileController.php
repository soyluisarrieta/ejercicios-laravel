<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UpdateProfileController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        auth()->user()->update($request->validated());
        $user = UserResource::make(auth()->user()->fresh());
        return jsonResponse(compact('user'));
    }
}
