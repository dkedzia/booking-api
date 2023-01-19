<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is missing.',
            'last_name.required' => 'Last name is missing.',
            'date_from.required' => 'Date from is missing.',
            'date_to.required' => 'Date to is missing.',
        ];
    }
}
