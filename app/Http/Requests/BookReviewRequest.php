<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookReviewRequest extends FormRequest
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
            'rating' => ['required'],
            'comment' => ['required','string']
        ];
    }
    public function messages()
    {
        return [
            'rating.required' => '評価数を選択してください。',
            'comment.required' => 'レビューは必須です。',
            'comment.string' => 'レビューは文字列で入力してください。'
        ];
    }
}
