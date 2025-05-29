<?php

namespace App\Http\Resources\web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'website_title' => $this->website_title,
            'slogan' => $this->slogan,
            'headerlogo' => asset($this->headerlogo),
            'footerlogo' => asset($this->footerlogo),
            'favicon' => asset($this->favicon),
            'location' => $this->location,
            'email' => $this->email,
            'webmail' => $this->webmail,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'telephone' => $this->telephone,
            'googlemap' => $this->googlemap,
            'websitelink' => $this->websitelink,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'youtube' => $this->youtube,
            'copyrighttext' => $this->copyrighttext,
            'tramscondition' => $this->tramscondition,
            'privacypolicy' => $this->privacypolicy,
            'refundpolicy' => $this->refundpolicy,
        ];
    }
}
