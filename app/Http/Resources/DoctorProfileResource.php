<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorProfileResource extends JsonResource
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
            'designation' => $this->designation,
            'description' => $this->description,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'department' => $this->department,
            'specialization' => $this->specialization,
            'experience_years' => $this->experience_years,
            'education' => $this->education,
            'chamber_address' => $this->chamber_address,
            'available_days' => $this->available_days,
            'available_time' => $this->available_time,
            'sorting_index' => $this->sorting_index,
            'status' => $this->status
        ];
    }
}
