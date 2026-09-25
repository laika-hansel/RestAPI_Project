<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        $isPut = $this->isMethod('put');

        return [
            'title' => [$isPut ? 'required' : 'sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => [$isPut ? 'required' : 'sometimes', 'required', 'string', 'in:todo,in-progress,done'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'title.string' => 'The task title must be a valid text string.',
            'title.max' => 'The task title may not be greater than 255 characters.',
            'status.required' => 'The task status is required.',
            'status.in' => 'The status must be one of: todo, in-progress, done.',
            'due_date.date' => 'The due date must be a valid date (e.g. YYYY-MM-DD).',
        ];
    }

    /**
     * Always return a structured JSON response upon validation failure.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
