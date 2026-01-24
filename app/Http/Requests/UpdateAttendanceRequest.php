<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller using policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shift' => 'required|exists:work_shifts,id',
            'location' => 'required|exists:work_locations,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'shift.required' => 'Shift tidak boleh kosong',
            'shift.exists' => 'Shift yang dipilih tidak valid',
            'location.required' => 'Lokasi tidak boleh kosong',
            'location.exists' => 'Lokasi yang dipilih tidak valid',
        ];
    }
}
