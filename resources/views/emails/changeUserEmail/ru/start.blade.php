@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Вы запросили изменение своего e-mail на сайте <a href="{{config('app.url')}}">{{config('app.name')}}</a>! Если вы этого не делали, то просто проигнорируйте это сообщение.
    </p>
    <p>
        Для изменения e-mail, вам необходимо пройти по этой <a href="{{config('app.url')}}/new/email/{{$token}}" target="_blank">ссылке</a>.
    </p>
@stop