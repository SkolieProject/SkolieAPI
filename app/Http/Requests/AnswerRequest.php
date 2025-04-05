<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === "STDNT";
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "assay_id" => ["required", "exists:assays,id"],
            "answers" => ["required", "array"],
            "answers.*.question_id" => ["required", "exists:questions,id"],
            "answers.*.alternative_id" => ["required", "exists:alternatives,id"],
        ];
    }
}
