<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id'
        ];

        return $rules;
    }
}
