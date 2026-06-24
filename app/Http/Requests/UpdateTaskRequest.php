<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'task_name' => ['sometimes', 'required', 'string', 'max:100'],
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'assigned_to' => ['sometimes', 'required', 'exists:users,id'],
            'status' => ['sometimes', 'required', 'in:Pending,In Progress,On Hold,Completed'],
            'priority' => ['sometimes', 'required', 'in:Low,Medium,High,Critical'],
            'deadline' => ['sometimes', 'required', 'date'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
