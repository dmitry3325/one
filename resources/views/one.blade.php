<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if(isset($app_settings['title']) && $app_settings['title'])
            {{$app_settings['title']}}
        @endif
    </title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">-->

    <!-- Styles -->
    <link rel="stylesheet" href="/css/app.css">
    @stack('style')
</head>
<body>
@if(\Auth::user())
    @include('general.menu')
@endif
<div id="content">
    @yield('content')
</div>

<footer>
    <script type="text/javascript" src="/js/core.js"></script>
    @stack('scripts')
</footer>
</body>
</html>
