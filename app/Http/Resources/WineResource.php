<?php

namespace App\Http\Resources;

use App\Http\Resources\PhotoResource;
use App\Models\Wine;
use Illuminate\Http\Resources\Json\JsonResource;

class WineResource extends JsonResource
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
            'id' => (string) $this->id,
            'type' => 'Wine',
            'attributes' => [
                'wine_uuid' => (string) $this->code,
                'name' => (string) $this->name,
                'description' => (string) $this->description,
                'colour' => (string) $this->colour,
                'effervescence' => (string) $this->effervescence,
                'sweetness' => (string) $this->sweetness,
                'year' => (string) $this->year,
                'created_at' => (string) $this->created_at,
                'updated_at' => (string) $this->updated_at,
            ],
            'images' => PhotoResource::collection($this->whenLoaded('photos'))
        ];
    }
}
