<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
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
            'name' => 'required|min:3|max:100',
            'slug' => 'required|min:3|max:255|unique:restaurants,slug,' . $this->restaurant->id,
            'description' => 'required|min:10|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        $slug = $this->restaurant->slug;
        if ($this->get('name') !== $this->restaurant->name) {
            $slug = str($this->get('name') . ' ' . uniqid())->slug();
        }
        $this->merge(['slug' => $slug]);
    }
}
