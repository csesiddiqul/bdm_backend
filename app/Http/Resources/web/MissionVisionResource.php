<?php

namespace App\Http\Resources\web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionVisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mission_title' => $this->mtitle,
            'mission_image' => asset($this->mimage),
            'mission_description' => $this->mdescription,
            'vision_title' => $this->vtitle,
            'vision_image' => asset($this->vimage),
            'vision_description' => $this->vdescription,
        ];
    }
}
