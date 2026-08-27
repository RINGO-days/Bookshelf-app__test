<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class IndexBookRequest extends FormRequest
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
            'per_page' => ['integer'],
            'genre' => ['string'],
            'published_date' => ['date'],
            'keyword' => ['string']
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'per_page.integer' => '１ページの表示数の指定は整数で指定してください。',
            'genre.string' => 'ジャンル名を正しく入力してください。',
            'published_date.date' => '出版日はYYYY-MM-DD形式で入力してください。',
            'keyword.string' => '正しいキーワードを入力してください。'
        ];
    }
}
