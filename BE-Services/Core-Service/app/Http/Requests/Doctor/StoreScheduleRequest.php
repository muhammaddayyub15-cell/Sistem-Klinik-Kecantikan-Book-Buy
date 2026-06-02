<?php


namespace App\Http\Requests\Doctor;
use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi role sudah ditangani middleware 'role:admin'
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.in'       => 'day_of_week harus salah satu dari: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday.',
            'start_time.date_format' => 'Format start_time harus HH:MM (contoh: 08:00).',
            'end_time.date_format'   => 'Format end_time harus HH:MM (contoh: 17:00).',
            'end_time.after'         => 'end_time harus setelah start_time.',
        ];
    }
}