<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:150',
            'slug' => 'sometimes|string|max:150',
            'description' => 'sometimes|string',
            'price' => 'required|numeric',
            'offer_price' => 'sometimes|numeric',
            'image' => 'sometimes|mimes:png,jpg,web,webp,jpge|max:2048',
            'stock' => 'required|integer',
            'is_featured' => 'boolean',
        ];
    }
}
