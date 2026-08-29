<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreCreateRequest extends FormRequest
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
            'name' => ['required','max:20','string']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ジャンル名は必須です。',
            'name.max' => 'ジャンル名の最大文字数は20文字です。',
            'name.string' => 'ジャンル名は文字列で入力してください。'
        ];
    }
}
