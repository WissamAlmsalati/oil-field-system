<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyServiceLogRequest extends FormRequest
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
            'field' => 'required|string|max:255',
            'well' => 'required|string|max:255',
            'contract' => 'required|string|max:255',
            'job_no' => 'required|string|max:255',
            'date' => 'required|date',
            'linked_job_id' => 'nullable|string|max:255',
            'personnel' => 'nullable|array',
            'personnel.*.name' => 'required_with:personnel|string|max:255',
            'personnel.*.position' => 'nullable|string|max:255',
            'personnel.*.hours' => 'nullable|numeric|min:0',
            'equipment_used' => 'nullable|array',
            'equipment_used.*.name' => 'required_with:equipment_used|string|max:255',
            'equipment_used.*.hours' => 'nullable|numeric|min:0',
            'almansoori_rep' => 'nullable|array',
            'almansoori_rep.*.name' => 'required_with:almansoori_rep|string|max:255',
            'almansoori_rep.*.position' => 'nullable|string|max:255',
            'mog_approval_1' => 'nullable|array',
            'mog_approval_1.name' => 'nullable|string|max:255',
            'mog_approval_1.signature' => 'nullable|string',
            'mog_approval_1.date' => 'nullable|date',
            'mog_approval_2' => 'nullable|array',
            'mog_approval_2.name' => 'nullable|string|max:255',
            'mog_approval_2.signature' => 'nullable|string',
            'mog_approval_2.date' => 'nullable|date',
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
            'field.required' => 'Field is required.',
            'well.required' => 'Well is required.',
            'contract.required' => 'Contract is required.',
            'job_no.required' => 'Job number is required.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'personnel.*.name.required_with' => 'Personnel name is required when personnel is provided.',
            'equipment_used.*.name.required_with' => 'Equipment name is required when equipment is provided.',
            'almansoori_rep.*.name.required_with' => 'Almansoori representative name is required when representative is provided.',
        ];
    }
} 