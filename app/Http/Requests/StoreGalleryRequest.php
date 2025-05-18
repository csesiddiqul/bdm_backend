<?php

namespace App\Http\Requests;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    use ApiErrorResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:photo,video', // Type must be photo or video
            'image' => 'required_if:type,photo|image|mimes:jpeg,png,jpg,gif|max:2048', // Image required if type is photo
            'video_url' => 'nullable|required_if:type,video|url', // Video URL required if type is video, otherwise nullable
            'sorting_index' => 'required|integer|min:0',
            'status' => 'required|in:1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be either photo or video.',
            'image.required_if' => 'The image field is required when the type is photo.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image must not be larger than 2MB.',
            'video_url.required_if' => 'The video URL field is required when the type is video.',
            'video_url.url' => 'The video URL must be a valid URL.',
            'sorting_index.integer' => 'The sorting index must be an integer.',
            'sorting_index.min' => 'The sorting index must be at least 0.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Active or Inactive.',
        ];
    }
}
