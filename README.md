# docker-apache-example
Docker + Apache + Laravel の Web アプリケーションサンプル

詳細は以下を参照
https://kita127.hatenablog.com/entry/2022/10/02/145614


## description

### Initialize

```
$ make init
```


### Terminate

```
$ make down
```

## Enviroment

- Apache
- MySQL
- PHP
- Laravel
- Node.js
- xdebug


## Vite

docker コンテナで動かしているせいか `npm run dev` しても Vite からフロントのソースがとれない.
かわりに `npm run build` してコンパイルしたものを提供する｡