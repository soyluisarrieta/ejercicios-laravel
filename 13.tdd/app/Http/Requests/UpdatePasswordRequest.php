<?php

namespace App\Http\Requests;

use App\Rules\CheckPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // "old_password" => 'required|string|min:8|current_password:api', // <-- Esta es la correcta forma de validar la contraseña actual
            "old_password" => ['required', 'min:8', new CheckPasswordRule],    // Pero así quedaría con una rule
            "password" => "required|min:8|confirmed",
        ];
    }
}
