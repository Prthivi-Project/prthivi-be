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
            "name" => ["required", "string", "alpha"],
            "description" => ["required", "string"],
            "address" => ["required", "string"],
            "photo_url" => ["url", "required_if:store_image,file"],
            "store_image" => ["required_if:photo_url,url", "file", "mimes:png,jpg,webp"],
            "map_location" => ["nullable", "string"],
        ];
    }
}
