<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $book = Book::firstOrCreate(
            ['isbn' => '9784101010014'],
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'published_date' => '1905-01-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
                'description' => '人間を客観的かつユーモラスに描写した、夏目漱石のデビュー作にして不朽の名作。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['1']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784422100524'],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'published_date' => '1936-10-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
                'description' => '世界中で読み継がれる人間関係のバイブル。人の心を動かすための原則を学ぶ。',
                'user_id' => $user->id

            ]
        );
        $book->genres()->sync(['2','4']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784873115658'],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'published_date' => '2012-06-23',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
                'description' => 'より良いコードを書くためのシンプルで実践的なテクニックを解説したエンジニア必読の書。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['3']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784863940246'],
            [
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'published_date' => '2013-08-30',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
                'description' => '個人の成功から他者との協調、自己変革へと導く世界的名著。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['2','4']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784101010021'],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'published_date' => '1906-04-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
                'description' => '無鉄砲で正義感の強い江戸っ子教師が、四国の地方中学で巻き起こす痛快な騒動を描く。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['1']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784309226712'],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'published_date' => '2016-09-08',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
                'description' => '虚構（フィクション）を信じる能力を武器に地球の覇者となった人類の歴史を壮大に紐解く。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['6','7']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784048930598'],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'published_date' => '2017-12-18',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
                'description' => '保守性が高く、美しく読みやすいコードを書くための原則とプラクティスを網羅。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['3']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784478025819'],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'published_date' => '2013-12-13',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
                'description' => 'アドラー心理学の教えを対話形式で分かりやすく紐解き、自由な生き方を提案するベストセラー。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['4']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784163902302'],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'published_date' => '2015-03-11',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
                'description' => 'お笑い芸人の世界を通して人間の誇りと葛藤を描き、芥川賞を受賞した感動の小説。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['1']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784822289607'],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'published_date' => '2019-01-11',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
                'description' => 'データやファクトに基づいて、世界を正しく見るための習慣と視点を身につける。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['4','7']);

        $book = Book::firstOrCreate(
            ['isbn' => '9784822251468'],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'published_date' => '2007-01-18',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
                'description' => 'たったひとつの鉄製コンテナが、世界経済とグローバル流通の仕組みをどう変えたのかを描くノンフィクション。',
                'user_id' => $user->id
            ]
        );
        $book->genres()->sync(['4','6']);
    }
}
