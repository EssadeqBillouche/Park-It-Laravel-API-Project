<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date', 'before:end_time'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'status' => ['required', 'in:active,completed,cancelled'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'position_id' => ['required', 'exists:positions,id'],
        ];
        
    }
}
