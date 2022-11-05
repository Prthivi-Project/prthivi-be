<?php

namespace App\Http\Requests;

use App\Models\LandingPage\SectionImages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSectionImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('update', SectionImages::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "section_id" => "numeric|exists:sections,id",
            'section_images' => "image|mimes:png,jpg,webp",
            'section_images_64base' => "string|base64image|base64mimes:png,jpg,webp|base64max:2096",
        ];
    }
}
