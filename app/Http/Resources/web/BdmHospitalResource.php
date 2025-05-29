<?php

namespace App\Http\Resources\web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BdmHospitalResource extends JsonResource
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
            'title' => $this->title,
            'image' => asset($this->image),
            'description' => $this->description,
        ];
    }
}
