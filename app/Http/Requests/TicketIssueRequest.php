<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketIssueRequest extends FormRequest
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
        return [
            'ticket_id' => 'required|exists:service_tickets,id',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:Open,In Progress,Resolved',
            'remarks' => 'nullable|string|max:1000',
            'date_reported' => 'required|date'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Service ticket is required.',
            'ticket_id.exists' => 'Selected service ticket does not exist.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: Open, In Progress, Resolved.',
            'remarks.string' => 'Remarks must be a string.',
            'remarks.max' => 'Remarks cannot exceed 1000 characters.',
            'date_reported.required' => 'Date reported is required.',
            'date_reported.date' => 'Date reported must be a valid date.'
        ];
    }
} 