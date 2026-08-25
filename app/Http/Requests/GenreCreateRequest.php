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
            'name' => ['required','max:20']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ジャンル名を入力して下さい。',
            'name.max' => 'ジャンル名の最大文字数は20文字です。'
        ];
    }
}
