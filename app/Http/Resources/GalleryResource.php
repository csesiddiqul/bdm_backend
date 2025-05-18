<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'type' => $this->type,
            'image'=>asset(path: $this->image),
            'video_url' => $this->video_url,
            'sorting_index' => $this->sorting_index,
            'status' => $this->status == 1 ? 'Active':'InActive'
        ];
    }
}
