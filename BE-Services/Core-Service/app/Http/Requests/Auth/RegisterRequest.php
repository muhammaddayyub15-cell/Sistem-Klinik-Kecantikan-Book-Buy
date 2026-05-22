<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    // authorize: endpoint publik, semua boleh akses
    public function authorize(): bool
    {
        return true;
    }

    // prepareForValidation: set default role sebelum validasi berjalan
    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => $this->role ?? 'patient',
        ]);
    }

    // rules: validasi data registrasi
    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            // admin dikeluarkan dari endpoint publik
            // admin hanya bisa dibuat via seeder atau panel internal
            'role'                  => 'nullable|in:doctor,patient,staff',
            'password_confirmation' => 'required',
        ];
    }

    // messages: pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'name.required'                  => 'Nama wajib diisi.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email sudah terdaftar.',
            'password.required'              => 'Password wajib diisi.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'role.in'                        => 'Role tidak valid.',
        ];
    }
}