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
            'task_name' => ['required', 'string', 'max:100'],
            'project_id' => ['required', 'exists:projects,id'],
            'assigned_to' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:Pending,In Progress,Completed'],
            'priority' => ['required', 'in:Low,Medium,High,Critical'],
            'deadline' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
