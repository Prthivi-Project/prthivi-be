<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            "description" => $this->description,
            'price' => $this->price,
            "status" => $this->status,
            "size" => $this->size,
            "store_id" => $this->store_id,
            $this->mergeWhen(($this->images !== null), [
                "product_images" => $this->images
            ], null),


        ];
    }
}
