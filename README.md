# Tubuyaki

ツイッター風つぶやきアプリケーション

## description

本アプリケーションの詳細な情報についてはWikiにまとめているためそちらも参照お願いします。

https://github.com/kita127/tubuyaki/wiki

## Usage

### Initialize

```
$ make init
```

### Terminate

```
$ make down
```

1. Initialize 実行
1. `apache` コンテナに入る
    1. `$ docker compose exec apache bash`
1. `$ npm run dev` を実行する
1.  ブラウザから `http://localhost:80` にアクセス

### Test

#### Back End

`apache`コンテナに入って以下のコマンドを実行する。

```
php artisan test --env=testing
```

#### Front End

```
npm run test
```