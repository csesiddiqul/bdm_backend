<?php

namespace App\Http\Resources\web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishersResource extends JsonResource
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
            'image' => asset($this->image),
            'sorting_index' => $this->sorting_index,
            'status' => $this->status
        ];
    }
}
