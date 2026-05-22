<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    // authorize: menentukan akses request
    public function authorize(): bool
    {
        return true;
    }

    // rules: validasi data registrasi
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|in:admin,doctor,patient,staff',
        ];
    }
}