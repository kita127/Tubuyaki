<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Login</title>
</head>

<body>
    <section>
        <h1>Top</h1>

        <section>
            <div>
                <p>ログイン後の画面サンプル</p>
            </div>
            <div>
                <form action="{{ url('/logout') }}" method="POST">
                    {{ csrf_field() }}
                    <div>
                        <input type="submit" value="ログアウト">
                    </div>
                </form>

            </div>
        </section>

    </section>

</body>

</html>
