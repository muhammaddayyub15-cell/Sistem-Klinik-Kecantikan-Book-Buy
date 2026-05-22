<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    // authorize: menentukan apakah request boleh dilakukan
    public function authorize(): bool
    {
        return true;
    }

    // rules: validasi input login
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }
}