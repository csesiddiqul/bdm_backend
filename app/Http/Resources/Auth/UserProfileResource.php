<?php

namespace App\Http\Resources\Auth;

use App\Enums\ApprovedStatusEnum;
use App\Http\Resources\BloodDonor\BloodDonorInfoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'profile_image' => $this->profile_image ? asset($this->profile_image) : NULL,
            'status' => $this->status ? ApprovedStatusEnum::from($this->status)->getLabelText() : null,
            'bts_id' => $this->bts_id,
            'email_verified_at' => $this->email_verified_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $this->updated_at->format('Y-m-d\TH:i:s.u\Z'),
            'blood_donor_info' => new BloodDonorInfoResource($this->bloodDonorInfo),

        ];
    }
}
