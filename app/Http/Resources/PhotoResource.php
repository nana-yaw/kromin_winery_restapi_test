<?php

namespace App\Http\Resources;

use App\Http\Resources\WineResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'Photo',
            'attributes' => [
                'id' => (string) $this->id,
                'name' => (string) $this->name,
                'image_url' => $this->url,
                'created_at' => (string) $this->created_at,
                'updated_at' => (string) $this->updated_at,
            ],
            'wine' => (new WineResource($this->whenLoaded('wine'))),
        ];
    }
}
