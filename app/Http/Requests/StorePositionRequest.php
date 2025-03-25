<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
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
            'number' => ['required', 'string', 'max:255', 'unique:positions,number'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,occupied,reserved'],
            'parking_id' => ['required', 'exists:parkings,id'],
        ];
    }
}
