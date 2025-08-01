<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubAgreementRequest extends FormRequest
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
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ];

        // For updates, make client_id optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['client_id'] = 'sometimes|exists:clients,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Client is required.',
            'client_id.exists' => 'Selected client does not exist.',
            'name.required' => 'Agreement name is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'balance.required' => 'Balance is required.',
            'balance.numeric' => 'Balance must be a valid number.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'document.mimes' => 'Document must be a PDF, DOC, or DOCX file.',
            'document.max' => 'Document size cannot exceed 10MB.'
        ];
    }
}
