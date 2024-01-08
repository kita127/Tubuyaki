<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Login</title>
</head>

<body>
    <section>
        <h1>Login</h1>

        <div>
            <form action="{{ url('/login') }}" method="POST">
                {{ csrf_field() }}
                <div>
                    <p>email</p>
                    <input type="text" name="email">
                </div>
                <div>
                    <p>password</p>
                    <input type="text" name="password">
                </div>
                <div>
                    <!-- 送信ボタン -->
                    <input type="submit" value="送信">
                </div>
            </form>
        </div>

    </section>

</body>

</html>
