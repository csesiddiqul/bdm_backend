<?php

namespace App\Http\Requests;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorProfileRequest extends FormRequest
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
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('doctor_profile')),
            ],

            'phone' => [
                'required',
                'regex:/^(\+8801[3-9]\d{8}|01[3-9]\d{8})$/',
                function ($value, $fail) {
                    $normalizedPhone = normalizePhone($value);
                    if (\App\Models\User::where('phone', $normalizedPhone)->where('id', '!=', $this->route('doctor_profile'))->exists()) {
                        $fail('Phone number is already registered.');
                    }
                },
            ],

            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
                'confirmed',
            ],

            'password_confirmation' => 'nullable|required_with:password|same:password',

            // Doctor profile specific
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'chamber_address' => 'nullable|string|max:255',
            'available_days' => 'nullable|string|max:255',
            'available_time' => 'nullable|string|max:255',
            'sorting_index' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name cannot exceed 255 characters.',

            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'The phone number must be a valid Bangladeshi number.',

            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already registered.',

            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required_with' => 'The password confirmation field is required when password is present.',
        ];
    }
}
