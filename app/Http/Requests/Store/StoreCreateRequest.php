<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreateRequest extends FormRequest
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
            "name" => ["required", "string"],
            "description" => ["required", "string"],
            "address" => ["required", "string"],
            "photo_url" => ["url", "required_if:store_image,file"],
            "store_image" => ["required_if:photo_url,null", "file", "mimes:png,jpg,webp"],
            "store_image_base64" => ['string', 'base64image', "base64max:2098"],
            "map_location" => ["nullable", "string"],
        ];
    }
}
