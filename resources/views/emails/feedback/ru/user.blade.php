@extends('emails.layouts.plain')

@section('content')
    Здравствуйте!

    Ваше сообщение на сайте <a href="{{config('app.url')}}">{{ config('app.name') }}</a> получено. Ожидайте ответа.
@stop