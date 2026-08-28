<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'title' => ['string','max:255'],
            'author' => ['string','max:255'],
            'isbn' => ['integer','digits:13'],
            'published_date' => ['date'],
            'descrioption' => ['text','max:255'],
            'image_url' => ['url','regex:/\.(jpg|jpeg|png)$/i'],
            'genres' => ['array'],
        ];
    }
    public function messages()
    {
        return [
            'title.max' => 'タイトルは225文字以内で入力してください。',
            'author.max' => '著者は225文字以内で入力してください。',
            'isbn.integet' => 'ISBNは数字のみで入力してください。',
            'isbn.digits' => 'ISBNは13桁のコードで入力してください。',
            'published_date.date' => '出版日はYYYY-MM-DDの形式で入力してください。',
            'description.max' => '書籍の説明の最大文字数は255文字です。',
            'image_url.url' => '書籍の画像はURLを入力してください。',
            'image_url.regex' => '書籍の画像の形式はjpeg形式、もしくはpng形式で入力してください。',
            'genres.required' => 'ジャンルを入力してください。'
        ];
    }
}
