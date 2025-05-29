<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWishersRequest extends FormRequest
{
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sorting_index' => 'required|integer|min:0',
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
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Active or Inactive.',
        ];
    }
}
