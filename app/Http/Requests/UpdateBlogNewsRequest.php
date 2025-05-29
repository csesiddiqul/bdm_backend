<?php

namespace App\Http\Requests;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogNewsRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sorting_index' => 'required|integer|min:0',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:1,0',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'The image field is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image must not be larger than 2MB.',
            'sorting_index.integer' => 'The sorting index must be an integer.',
            'sorting_index.min' => 'The sorting index must be at least 0.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a valid string.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Active or Inactive.',
        ];
    }
}
