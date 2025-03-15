<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
            'role' => ['required', 'in:STDNT,TCHR'],
            'subject_id' => ['required_if:role,TCHR'],
            'classes_ids' => ['required_if:role,TCHR'],
            'class_tag_id' => ['required_if:role,STDNT']
        ];
    }
}
