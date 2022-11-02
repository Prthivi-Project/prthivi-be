<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => ['string'],
            'description' => ['string'],
            "price" => ['numeric'],
            "status" => ["string",  "in:available,reserved"],
            "size" => ["string"],
            'product_image' => ['nullable'],
            'product_image.*' => ['file', 'mimes:png,jpg,webp'],
            "fabric_composition" => ["string"],
            "store_id" => ["exists:stores,id"],
        ];
    }
}
