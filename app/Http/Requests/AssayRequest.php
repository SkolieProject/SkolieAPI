<?php

namespace App\Http\Requests;

use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AssayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        $teacher = Teacher::where('user_id', $this->user()->id)->first();
        if (! $teacher) {
            return false;
        }
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
            "title" => ["required", "string"],
            "deadline" => ["required", "date"],
            "subject_id" => ["required", "exists:subjects,id"],
            "class_tag_id" => ["exists:class_tags,id"],
            "questions" => ["array"],
            "questions.*.asking" => ["required", "string"],
            "questions.*.alternatives" => ["array"],
            "questions.*.alternatives.*.description" => ["required", "string"],
            "questions.*.alternatives.*.is_correct" => ["bool"]
        ];
    }



    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questions = $this->input('questions', []);

            foreach ($questions as $index => $question) {
                $alternatives = $question['alternatives'] ?? [];
                $hasCorrectAlternative = false;

                foreach ($alternatives as $alternative) {
                    if (isset($alternative['is_correct']) && $alternative['is_correct']) {
                        $hasCorrectAlternative = true;
                        break;
                    }
                }

                if (! $hasCorrectAlternative) {
                    $validator->errors()->add("questions.$index.alternatives", "Cada questão deve conter uma alternativa correta.");
                }
            }
        });
    }
}
