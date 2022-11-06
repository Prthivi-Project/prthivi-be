<?php

namespace App\Http\Requests;

use App\Models\LandingPage\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLandingPageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('update', Section::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "section_images_64base" => "nullable",
            "section_images_64base.*" => "base64image|base64mimes:png,jpg,webp|base64max:2098",
            "section_images" => "nullable",
            "section_images.*" => "file|mimes:png,jpg,webp",
            "number" => "numeric|unique:sections,number",
            "section_title" => "string",
            "section_description" => "string",
            'button_link' => 'string',
            'button_name' => 'required_if:button_link,string|string'
        ];
    }
}
