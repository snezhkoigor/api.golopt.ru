@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Ваш e-mail адрес был успешно изменен на сайте <a href="{{config('app.url')}}">{{ config('app.name') }}</a>.
    </p>
@stop