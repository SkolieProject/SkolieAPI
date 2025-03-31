<?php

namespace App\Http\Requests;

use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssayRewriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        
        $teacher = Teacher::where('user_id', $this->user()->id)->firstOrFail();
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
            "id" => ['exists:assays,id', 'integer'],
            "title" => ["string"],
            "deadline" => ["date", "format:Y-m-d"],
            "subject_id" => ["exists:subjects,id"],
            "class_tag_id" => ["exists:class_tags,id"],
            "questions" => ["array"],
            "questions.*.id" => ["exists:questions,id", "integer"],
            "questions.*.question_text" => ["required", "string"],
            "questions.*.correct_alternative" => ["required", "integer"],
            "questions.*.alternatives" => ["array"],
            "questions.*.alternatives.*.id" => ["exists:alternatives,id", "integer"],
            "questions.*.alternatives.*.alternative_text" => ["required", "string"],
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
                    $validator->errors()->add("questions.$index.alternatives", "Cada questão deve conter uma alternaticva correta.");
                }
            }
        });
    }
}
