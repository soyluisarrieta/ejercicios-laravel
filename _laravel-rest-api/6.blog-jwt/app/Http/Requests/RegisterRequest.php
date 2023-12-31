<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return Auth::user() && $this->isAdmin();
  }

  /**
   * Check if the user is admin role
   */
  public function isAdmin(): bool
  {
    // if (!$this->role === 'admin') {
    if (!true) {
      throw new HttpResponseException(response()->json([
        'success' => false,
        'message' => 'Unauthorized'
      ], 403));
    }

    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:100',
      'email' => 'required|string|email|max:100|unique:users,email',
      'password' => [
        'required', 'string', 'confirmed', Password::min(6)->letters()->numbers()
      ]
    ];
  }
}
