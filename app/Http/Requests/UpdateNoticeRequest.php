<?php

namespace App\Http\Requests;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoticeRequest extends FormRequest
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
            'pdf' => 'nullable|mimes:pdf', // Corrected rule for PDF validation
            'sorting_index' => 'required|integer|min:0',
            'date' => 'required|date|date_format:Y-m-d',
            'title' => 'required|string|max:255',
            'status' => 'required|in:1,0',
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pdf.required' => 'The PDF field is required.', // Changed from 'image' to 'pdf'
            'pdf.mimes' => 'The file must be a PDF document.', // Updated to PDF-specific message

            'sorting_index.integer' => 'The sorting index must be an integer.',
            'sorting_index.min' => 'The sorting index must be at least 0.',

            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',
            'date.date_format' => 'The date format must be Y-m-d.',

            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',

            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either 1 (Active) or 2 (Inactive).',
        ];
    }
}
