<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('create', Product::class);
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
            "status" => ['required', "string",  "in:available,reserved"],
            "size" => ["string"],
            "fabric_composition" => ["required", "string"],
            'product_image' => ['nullable'],
            'product_image.*' => ['image', 'mimes:png,jpg,webp'],
            'product_image_base64' => ['nullable'],
            'product_image_base64.*.image' => ["required_if:product_image_base64,array", 'base64image'],
            'product_image_base64.*.color_id' => ['numeric', 'exists:colors,id'],
            'product_image_base64.*.priority_level' => ['numeric'],
        ];
    }
}
