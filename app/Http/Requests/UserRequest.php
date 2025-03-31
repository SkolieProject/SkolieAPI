<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'ADMIN';
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
            'email' => ['required', 'email', 'not_in:users,email'], 
            'password' => 'required',
            'role' => ['required', 'in:STDNT,TCHR,ADMIN'],
            'subject_id' => ['required_if:role,TCHR', 'exists:subjects,id'],
            'classes_ids' => ['required_if:role,TCHR', 'array', 'exists:class_tags,id'],
            'class_tag_id' => ['required_if:role,STDNT', 'exists:class_tags,id'],
        ];
    }
}
