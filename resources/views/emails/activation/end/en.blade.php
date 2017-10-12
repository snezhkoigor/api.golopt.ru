@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        Your account has been successfully activated on the website {{config('app.name')}}.
    </p>
    <p>
        Next you need <a href="{{config('app.url')}}/login" target="_blank">Log in</a>.
    </p>
@stop