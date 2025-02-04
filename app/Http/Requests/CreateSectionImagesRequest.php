<?php

namespace App\Http\Requests;

use App\Models\LandingPage\SectionImages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateSectionImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('create', SectionImages::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'section_id' => "required|numeric|exists:sections,id",
            'section_images' => "required_if:section_images_64base,null|image|mimes:png,jpg,webp",
            'section_images_64base' => "required_if:section_images,null|string|base64image|base64mimes:png,jpg,webp|base64max:2096",

        ];
    }
}
