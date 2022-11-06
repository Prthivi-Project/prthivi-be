<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageCreateRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'product_image' => ['file', 'mimes:png,jpg,webp'],
            'product_image_base64' => ["base64image"],
            'color_id'  => ['numeric', 'exists:colors,id'],
            'priority_level' => ['numeric'],
            "image_url" => "url"
        ];
    }
}