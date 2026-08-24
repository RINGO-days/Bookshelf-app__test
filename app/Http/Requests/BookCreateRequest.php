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
            'title' => ['required'],
            'author' => ['required'],
            'isbn' => ['required','integer','digits:13'],
            'published_date' => ['required','date'],
            'description' => ['nullable','max:255'],
            'image_url' => ['nullable','url','regex:/\.(jpg|jpeg|png)$/i'],
            'genres' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'author.required' => '著者は必須です。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.integet' => 'ISBNは数字のみで入力して下さい。',
            'isbn.digits' => 'ISBNは13桁のコードで入力して下さい。',
            'published_date' => '出版日は必須です。',
            'published_date.date' => '出版日はYYYY-MM-DDの形式で入力して下さい。',
            'description.max' => '書籍の説明の最大文字数は255文字です。',
            'image_url.url' => '書籍の画像はURLを入力して下さい。',
            'image_url.regex' => '書籍の画像の形式はjpeg形式、もしくはpng形式で入力して下さい。',
            'genres.required' => 'ジャンルを選んで下さい。'
        ];
    }
}
