<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts|string|max:255',
            'contacts.*.email' => 'required_with:contacts|email|max:255',
            'contacts.*.phone' => 'required_with:contacts|string|max:20',
            'contacts.*.position' => 'required_with:contacts|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Client name is required',
            'logo.image' => 'Logo must be an image file',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or GIF file',
            'logo.max' => 'Logo size must not exceed 2MB',
            'contacts.*.name.required_with' => 'Contact name is required',
            'contacts.*.email.required_with' => 'Contact email is required',
            'contacts.*.email.email' => 'Contact email must be valid',
            'contacts.*.phone.required_with' => 'Contact phone is required',
            'contacts.*.position.required_with' => 'Contact position is required',
        ];
    }
}
