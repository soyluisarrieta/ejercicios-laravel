<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
  function user()
  {
    return 'Authenticated user';
  }
}
