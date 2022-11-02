<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageRequest extends FormRequest
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
            "section_images_64base" => "array",
            "section_images_64base.*" => "base64image|base64mimes:png,jpg,webp|base64max:2098",
            "section_images" => "nullable",
            "section_images.*" => "file|mimes:png,jpg,webp",
            "number" => "numeric",
            "section_title" => "string",
            "section_description" => "string",
            'button_link' => 'string',
            'button_name' => 'required_if:button_link,string|string'
        ];
    }
}
