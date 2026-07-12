<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:office,home',
            'full_name' => 'required|min:2|max:150',
            'phone' => 'required|digits:10',
            'address_line1' => 'required',
            'address_line2' => 'nullable',
            'city' => 'required',
            'state' => 'required|min:2|max:150',
            'pincode' => 'required|numeric|digits:5',
            'country' => 'required|min:2|max:150',

        ];
    }
}
