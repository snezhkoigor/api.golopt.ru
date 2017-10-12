@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        Your e-mail address has been successfully changed on the website <a href="{{config('app.url')}}">{{ config('app.name') }}</a>.
    </p>
@stop