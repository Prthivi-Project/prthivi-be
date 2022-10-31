<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "number" => $request->number,
            "section_title" => $request->section_title,
            "section_description" => $request->section_description,
            "button_link" => $request->button_link,
            "button_name" => $request->button_name,
            "images" => $this->whenPivotLoaded('section_images', $this->images, null),
            "id" => $request->id,
        ];
    }
}
