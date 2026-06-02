<?php
namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'sometimes' → field boleh tidak dikirim (partial update)
            'day_of_week' => ['sometimes', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time'  => ['sometimes', 'date_format:H:i'],
            'end_time'    => ['sometimes', 'date_format:H:i'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.in'         => 'day_of_week harus salah satu dari: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday.',
            'start_time.date_format' => 'Format start_time harus HH:MM (contoh: 08:00).',
            'end_time.date_format'   => 'Format end_time harus HH:MM (contoh: 17:00).',
        ];
    }
}