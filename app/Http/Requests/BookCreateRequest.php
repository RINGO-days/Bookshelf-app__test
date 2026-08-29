<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookCreateRequest extends FormRequest
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
            'title' => ['required','string','max:255'],
            'author' => ['required','string','max:255'],
            'isbn' => ['required','string','digits:13'],
            'published_date' => ['required','date'],
            'description' => ['nullable','string'],
            'image_url' => ['nullable','url','string','max:255'],
            'genres' => ['required','array','exists:genres,name']
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'author.required' => '著者は必須です。',
            'author.string' => '著者は文字列で入力してください。',
            'author.max' => "著者は255文字以内で入力してください。",
            'isbn.required' => 'ISBNは必須です。',
            'isbn.integer' => 'ISBNは文字列で入力してください。',
            'isbn.digits' => 'ISBNは13桁で入力してください。',
            'published_date.required' => '出版日は必須です。',
            'published_date.date' => '出版日は有効な日付形式で入力してください。',
            'image_url.url' => '画像URLを入力してください。',
            'image_url.string' => '画像URLは文字列で入力してください。',
            'image_url.max' => '画像URLは255文字以内で入力してください。',
            'genres.required' => 'ジャンルは1つ以上を選択してください。',
            'genres.array' => 'ジャンルは配列で入力してください。',
            'genres.*.exists' => '選択されたジャンルは存在しません。'
        ];
    }
}
