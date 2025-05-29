<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMissionVisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow authorization for all users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mtitle' => 'required|string|max:255',
            'mimage' => 'nullable|file|mimes:jpeg,jpg,png,svg|max:2048',
            'mdescription' => 'required|string',
            'vtitle' => 'required|string|max:255',
            'vimage' => 'nullable|file|mimes:jpeg,jpg,png,svg|max:2048',
            'vdescription' => 'required|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mtitle.required' => 'The mission title field is required.',
            'mtitle.string' => 'The mission title must be a string.',
            'mimage.required' => 'The mission image field is required.',
            'mimage.file' => 'The mission image must be a file.',
            'mimage.mimes' => 'The mission image must be a file of type: jpeg, jpg, png, svg.',
            'mdescription.required' => 'The mission description field is required.',

            'vtitle.required' => 'The vision title field is required.',
            'vtitle.string' => 'The vision title must be a string.',
            'vimage.required' => 'The vision image field is required.',
            'vimage.file' => 'The vision image must be a file.',
            'vimage.mimes' => 'The vision image must be a file of type: jpeg, jpg, png, svg.',
            'vdescription.required' => 'The vision description field is required.',
        ];
    }
}
