<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductGetAllRequest extends FormRequest
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
            "perPage" => ["numeric", "min:0", "max:50"],
            "id" => ["numeric"],
            "name" => ["string"],
            "size" => ["string", 'alpha'],
            "status" => ["string", 'in:reserved,available'],
            "color" => ["alpha"],
            "orderBy" => ["string", 'in:most_viewed,newest'],
        ];
    }
}
