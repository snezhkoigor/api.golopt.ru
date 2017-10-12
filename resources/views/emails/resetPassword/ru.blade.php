@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Новый пароль авторизации в кабинете: <b>{{$password}}</b>
    </p>
    <p>
        Вы всегда его можете поменять в своем личном кабинете.
    </p>
@stop