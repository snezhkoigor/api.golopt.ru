@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Вам необходимо активировать свой <a href="{{config('app.url')}}/activate/{{$token}}" target="_blank">аккаунт</a>.
    </p>
@stop