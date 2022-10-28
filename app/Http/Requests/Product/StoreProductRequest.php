<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            "price" => ['required', 'numeric'],
            "available" => ["boolean"],
            "size" => ["string"],
            'product_image' => ['nullable'],
            'product_image.*' => ['image', 'mimes:png,jpg,webp'],
            "fabric_composition" => ["required", "string"]
        ];
    }
}
