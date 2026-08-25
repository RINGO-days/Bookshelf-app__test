<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'rating' => 'required',
            'comment' => ['required','max:255'],
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価数をつけて下さい。',
            'comment.required' => 'レビューを記載して下さい。',
            'comment.max' => 'レビューの文字数は255文字以内で記載して下さい。'
        ];
    }
}
