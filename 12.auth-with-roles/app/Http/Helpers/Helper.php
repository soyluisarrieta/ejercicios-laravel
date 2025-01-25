<?php

namespace App\Http\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;

class Helper
{
  public static function sendError($message, $errors = [], $code = 401)
  {
    $reponse = ['success' => false, 'message' => $message];

    if (!empty($errors)) {
      $reponse['data'] = $errors;
    }

    throw new HttpResponseException(response()->json($reponse, $code));
  }
}
