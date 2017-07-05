<?php
    $curPath = '/'.Request::path();
?>
<nav class="navbar navbar-inverse bg-inverse navbar-toggleable-md">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="/">ONE</a>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            @foreach($admin_bar as $app)
                @if(isset($app['children']))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{$app['url']}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{(isset($app['title'])?$app['title']:'')}}
                        </a>
                        <div class="dropdown-menu">
                            @foreach($app['children'] as $subUp)
                                <a class="dropdown-item {{($curPath == $subUp['url'])?'active':''}}" href="{{$subUp['url']}}">{{(isset($subUp['title'])?$subUp['title']:'')}}</a>
                            @endforeach
                        </div>
                    </li>
                @else
                    <li class="nav-item {{($curPath == $app['url'])?'active':''}}">
                        <a class="nav-link" href="{{$app['url']}}">{{(isset($app['title'])?$app['title']:'')}}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>