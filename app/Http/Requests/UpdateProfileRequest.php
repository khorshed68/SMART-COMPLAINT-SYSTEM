<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = auth()->id();
        return [
            'name' => 'required|string|min:2|max:100',
            'email' => "required|email|max:100|unique:users,email,{$userId}",
            'phone' => 'nullable|string|max:15',
            'department' => 'nullable|string|max:100',
        ];
    }
}
