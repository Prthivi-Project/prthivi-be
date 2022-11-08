<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            "name" => ["string", "alpha"],
            "description" => ["string"],
            "address" => ["string"],
            "photo_url" => ["url"],
            "store_image" => ["file", "mimes:png,jpg,webp"],
            "store_image_base64" => ['string', 'base64image', "base64mimeTypes:png,jpg,jpeg,webp", "base64max:2098"],
            "map_location" => ["nullable", "string"],
        ];
    }
}
