<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:Contract,Invoice,Report,Certificate,License,Manual,Procedure,Policy,Form,Other',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'client_id' => 'nullable|exists:clients,id',
            'is_public' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after:today',
            'metadata' => 'nullable|array',
        ];

        // Add file validation for create requests
        if ($this->isMethod('POST')) {
            $rules['file'] = 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip,rar';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Document title is required.',
            'title.string' => 'Document title must be a string.',
            'title.max' => 'Document title cannot exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'category.required' => 'Document category is required.',
            'category.in' => 'Category must be one of: Contract, Invoice, Report, Certificate, License, Manual, Procedure, Policy, Form, Other.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'client_id.exists' => 'Selected client does not exist.',
            'is_public.boolean' => 'Public status must be true or false.',
            'expiry_date.date' => 'Expiry date must be a valid date.',
            'expiry_date.after' => 'Expiry date must be after today.',
            'metadata.array' => 'Metadata must be an array.',
            'file.required' => 'Document file is required.',
            'file.file' => 'Uploaded file is invalid.',
            'file.max' => 'File size cannot exceed 10MB.',
            'file.mimes' => 'File type must be one of: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, jpg, jpeg, png, gif, zip, rar.',
        ];
    }
} 