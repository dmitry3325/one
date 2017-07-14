<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>login</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">-->

    <!-- Styles -->
    <link rel="stylesheet" href="/css/app.css">
    @stack('style')
    <style>
        body {
            background: #292b2c;
            color:#fff;
        }
        .form-signin {
            max-width: 330px;
            padding: 15px;
            outline: none;
            margin: 0 auto;
        }

        .form-signin, .form-signin .checkbox {
            margin-bottom: 10px;
        }

        .form-signin .checkbox {
            font-weight: normal;
        }

        .form-signin .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .login-title {
            margin: 14% 0 30px 0;
            font-size: 18px;
            font-weight: 400;
            display: block;
        }
    </style>
</head>
<body>
<div id="content">
    <h1 class="text-center login-title">One</h1>
    <form class="form-signin" action="/common/auth/?method=login" method="get">

        {!! csrf_field() !!}

        <input type="text" class="form-control" placeholder="Email" name="email" required autofocus>
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        <input type="hidden" name="method" value="login">
        <button class="btn btn-lg btn-primary btn-block" type="submit">
            вход
        </button>
        <label class="checkbox pull-left">
            <input type="checkbox" value="remember-me">
            Запомнить
        </label>

        @if(count($errors))
            <div class="form-item">
                @foreach($errors->all() as $e)
                    <div class="label error">{{$e}}</div>
                @endforeach
            </div>
        @endif
    </form>
</div>
</body>
</html>
