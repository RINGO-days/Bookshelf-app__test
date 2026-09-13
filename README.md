# 📖 bookshlef-app\_\_test

coachtech模擬案件　書籍管理アプリ

## 製作者

田代　晃世

## 概要

### 目的

クライアント（コーチ）とのアプリケーションの設計に関しての詳細を協議しながら、開発を進めることで、実際のアプリケーション開発を擬似的に経験する。

### アプリケーション説明

ユーザーが書籍を登録することで、他のユーザーからの評価を受けることができるBookShelfアプリ。
その他、読書完了までの期日を設定できる読書計画や、ユーザーがどのような書籍を評価しているかのレポート表示、APIを使用した書籍管理などの機能がある。

## 📃laravel環境構築

1. gitのクローン

```bash
git clone https://github.com/RINGO-days/Bookshelf-app__test.git
```

2. Dockerデスクトップアプリを立ち上げる
3. ワーキングディレクトリに移動

```bash
cd bookshelf-app__test
```

4. 環境設定ファイルの作成

```bash
cp .env.example .env
```

5. Laravel Sailのインストール

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html -e COMPOSER_CACHE_DIR=/tmp/composer_cache laravelsail/php82-composer:latest composer require laravel/sail --dev
```

6. Sailの設定ファイル（compose.yaml）の発行

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html -e COMPOSER_CACHE_DIR=/tmp/composer_cache laravelsail/php82-composer:latest php artisan sail:install --with=mysql
```

<details>
<summary style="cursor: pointer;">⚠️ Mac(Apple Silicon)をお使いの方</summary>

> Apple Silicon搭載のMacでは、docker-conpose up -d実行時に以下のエラーが発生することがあります
>
> ### 🚫症状
>
> `The requested image's platform (linux/amd64) does not match the detected host platform`
> などの警告が出て、作業が終了する場合がある。
>
> ### 💡対策
>
> docker-compose.ymlを開き、プラットフォームを明示する。
>
> ```text
> mysql:
>        image: mysql:8.0.26
>        platform: linux/amd64　←この行を追加
>        environment:
>        〜
> ```

</details>

## 🌲フロントエンドの設定

### 1. Dockerコンテナの起動

```bash
sail up -d
```

### 2. エイリアスの設定（以降、.vendor/bin/sailをsailに短縮してコマンドを使用するため）

```bash
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.zshrc
exec $SHELL
```

### 3. npmのインストール

```bash
sail npm install
```
⚠️OCI runtime exec failed: exec failed •••のエラーが出た場合は、一度**sail down**を実行し、**sail up -d**で再度、コンテナを立ち上げてください。
### 4. Alpine.jsのインストール

```bash
sail npm install alpinejs
```

### 5. Tailwind CSSと @tailwindcss/forms プラグインのインストール

```bash
sail npm install -D tailwindcss@^3.4.0 @tailwindcss/forms postcss autoprefixer
```

### 6. 設定ファイルの生成

```bash
sail npx tailwindcss init -p
```

### 7. Tailwind CSSのテンプレートパス設定とforms プラグインの有効化

**下記の内容をtailwind.config.jsに上書きしてください**

```
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
```

### 8. アプリケーションキーの作成

```bash
sail artisan key:generate
```

### 9. データベースおよび初期データの投入

```bash
sail artisan migrate:fresh --seed
```

### 10. Vite開発サーバーの起動

```bash
sail npm run dev
```

⚠️このコマンドは、コマンドが実行状態になります。以降のコマンド入力は別のターミナルから入力してください。
### (10. PHPunitでの各アクションの動作テスト )

```bash
sail artisan test --coverage
```

## 🛠使用技術

- Laravel 10.50.3
- Laravel Sail
- Tailwind CSS
- PHP 8.5.9
- mysql 8.4
- nginx 1.21.1
- phpMyAdmin
- Fortify
- Sanctum

## 📍開発環境

- http://localhost/books ホーム画面
- http://localhost/register 新規会員登録画面
- http://localhost/login ログイン画面
- http://localhost:8080 phpMyAdmin

## 🔑実装内容

要件シートに則った基本要件の実装

### 📃ER図

![ER図](ER.png)

### API

ルート設定はapiResourceを用いて、5エンドポイントを一括定義(index,store,show,update,destroy)。sunctum認証でstore,update,destroyの機能制限をしている。

#### index

- 書籍一覧を取得するアクション
- クエリパラメータによって、出版日、ジャンル、キーワード検索、１ページの表示件数の指定することで取得する書籍を絞り込むことができる。

#### show

- 書籍を取得するアクション
- 書籍IDをしてすることで、指定した書籍の情報を取得できる。

#### store(sunctum認証)

- 書籍を作成するアクション
- bodyデータとして書籍情報を入力し、送信することで、書籍を登録することができる。

#### update(sunctum認証)

- 書籍情報を更新するアクション
- bodyデータに更新する情報を入力し、送信することで、書籍を更新することができる。

#### destroy(sunctum認証)

- 書籍情報を更新するアクション
- 書籍IDを指定することで、書籍を削除することができる。
  実装<br>
- app/Policies/AttendanceRecordPolicy.phpを作成し、update,destroyのアクション時に本人または管理者の権限の確認を行う<br>
- Laravel Sanctumを導入しstore,update,destroyのルートにミドルウェアauth:sanctumを適用
