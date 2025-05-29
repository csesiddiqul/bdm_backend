<?php

namespace App\Http\Resources\web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
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
            'pdf' => asset($this->pdf),
            'date' => $this->date,
            'title' => $this->title,
            'sorting_index' => $this->sorting_index,
            'status' => $this->status
        ];
    }
}
