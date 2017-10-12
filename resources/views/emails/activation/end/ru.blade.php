@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Ваш аккаунт был успешно активирован на сайте {{config('app.name')}}.
    </p>
    <p>
        Для дальнейшей работы, вам необходимо <a href="{{config('app.url')}}/login" target="_blank">авторизоваться</a>.
    </p>
@stop