<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallOutJobRequest extends FormRequest
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
            'job_name' => 'required|string|max:255',
            'work_order_number' => 'required|string|max:100|unique:call_out_jobs,work_order_number',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|in:scheduled,in_progress,completed,cancelled',
            'priority' => 'nullable|string|in:low,medium,high',
            'description' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ];

        // For updates, make work_order_number unique excluding current record
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $jobId = $this->route('id');
            $rules['work_order_number'] = 'required|string|max:100|unique:call_out_jobs,work_order_number,' . $jobId;
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
            'job_name.required' => 'Job name is required.',
            'work_order_number.required' => 'Work order number is required.',
            'work_order_number.unique' => 'Work order number already exists.',
            'start_date.required' => 'Start date is required.',
            'end_date.after' => 'End date must be after start date.',
            'status.in' => 'Status must be one of: scheduled, in_progress, completed, cancelled.',
            'documents.*.mimes' => 'Documents must be PDF, DOC, DOCX, JPG, JPEG, or PNG files.',
            'documents.*.max' => 'Document size cannot exceed 10MB.'
        ];
    }
}
