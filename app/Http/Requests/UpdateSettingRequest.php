<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true if authorization logic is handled elsewhere
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
            'website_title' => 'required|string|max:255',
            'slogan' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'email' => 'required|email|max:255',

            'phone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'telephone' => 'nullable|string|max:20',
            'googlemap' => 'required',


            'websitelink' => 'required|max:255',
            'facebook' => 'nullable|max:255',
            'twitter' => 'nullable|max:255',
            'instagram' => 'nullable|max:255',
            'linkedin' => 'nullable|max:255',
            'youtube' => 'nullable|max:255',
            'copyrighttext' => 'required|string|max:500',


            'headerlogo' => 'nullable|file|mimes:jpeg,jpg,png,svg|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png|max:2048',

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
            'website_title.required' => 'The website title field is required.',
            'website_title.string' => 'The website title must be a string.',
            'slogan.required' => 'The slogan field is required.',
            'slogan.string' => 'The slogan must be a string.',
            'headerlogo.required' => 'The header logo field is required.',
            'headerlogo.file' => 'The header logo must be a file.',
            'headerlogo.mimes' => 'The header logo must be a file of type: jpeg, jpg, png, svg.',
            'headerlogo.max' => 'The footer logo may not be greater than 2 Mb.',
            'footerlogo.required' => 'The footer logo field is required.',
            'footerlogo.file' => 'The footer logo must be a file.',
            'footerlogo.mimes' => 'The footer logo must be a file of type: jpeg, jpg, png, svg.',
            'footerlogo.max' => 'The footer logo may not be greater than 2 Mb.',
            'favicon.required' => 'The favicon field is required.',
            'favicon.file' => 'The favicon must be a file.',
            'location.required' => 'The location field is required.',
            'email.required' => 'The email field is required.',
            'webmail.required' => 'The webmail field is required.',
            'phone.required' => 'The phone field is required.',
            'whatsapp.required' => 'The WhatsApp field is required.',
            'telephone.string' => 'The telephone must be a string.',
            'googlemap.required' => 'The Google map link field is required.',
            'googlemap.url' => 'The Google map link must be a valid URL.',
            'websitelink.required' => 'The website link field is required.',
            'websitelink.url' => 'The website link must be a valid URL.',
            'facebook.url' => 'The Facebook link must be a valid URL.',
            'twitter.url' => 'The Twitter link must be a valid URL.',
            'instagram.url' => 'The Instagram link must be a valid URL.',
            'linkedin.url' => 'The LinkedIn link must be a valid URL.',
            'youtube.url' => 'The YouTube link must be a valid URL.',
            'copyrighttext.required' => 'The copyright text field is required.',

            'tramscondition.required' => 'The trams & condition field is required.',
            'privacypolicy.required' => 'The privacy policy field is required.',
            'refundpolicy.required' => 'The refund policy field is required.',
        ];
    }
}
