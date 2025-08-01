<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceTicketRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'sub_agreement_id' => 'nullable|exists:sub_agreements,id',
            'call_out_job_id' => 'nullable|exists:call_out_jobs,id',
            'date' => 'required|date',
            'status' => 'required|in:In Field to Sign,Issue,Delivered,Invoiced',
            'amount' => 'required|numeric|min:0',
            'related_log_ids' => 'nullable|array',
            'related_log_ids.*' => 'integer|exists:daily_service_logs,id',
            'documents' => 'nullable|array',
            'documents.*.name' => 'required_with:documents|string|max:255',
            'documents.*.file_path' => 'nullable|string|max:500',
            'documents.*.file_type' => 'nullable|string|max:50',
            'documents.*.upload_date' => 'nullable|date'
        ];

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
            'sub_agreement_id.exists' => 'Selected sub-agreement does not exist.',
            'call_out_job_id.exists' => 'Selected call-out job does not exist.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: In Field to Sign, Issue, Delivered, Invoiced.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'related_log_ids.array' => 'Related log IDs must be an array.',
            'related_log_ids.*.integer' => 'Related log ID must be an integer.',
            'related_log_ids.*.exists' => 'Related log ID does not exist.',
            'documents.array' => 'Documents must be an array.',
            'documents.*.name.required_with' => 'Document name is required.',
            'documents.*.name.string' => 'Document name must be a string.',
            'documents.*.name.max' => 'Document name cannot exceed 255 characters.',
            'documents.*.file_path.string' => 'Document file path must be a string.',
            'documents.*.file_path.max' => 'Document file path cannot exceed 500 characters.',
            'documents.*.file_type.string' => 'Document file type must be a string.',
            'documents.*.file_type.max' => 'Document file type cannot exceed 50 characters.',
            'documents.*.upload_date.date' => 'Document upload date must be a valid date.'
        ];
    }
} 