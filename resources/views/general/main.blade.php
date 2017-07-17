@extends('one')

@push('scripts')
@if(isset($app['js']))
    @if(is_array($app['js']))
        @foreach($app['js'] as $src)
            <script type="text/javascript" src="/js/{{$src}}"></script>
        @endforeach
    @else
        <script type="text/javascript" src="{{$app['js']}}"></script>
    @endif
@endif
@endpush

@push('style')
@if(isset($app['css']))
    @if(is_array($app['css']))
        @foreach($app['css'] as $href)
            <link rel="stylesheet" href="/css/{{$href}}">
        @endforeach
    @else
        <link rel="stylesheet" href="/css/{{$app['css']}}">
    @endif
@endif
@endpush
