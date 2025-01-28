<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    public function update(Request $request)
    {
        auth()->user()->update($request->all());
        $user = UserResource::make(auth()->user()->fresh());
        return jsonResponse(compact('user'));
    }
}
