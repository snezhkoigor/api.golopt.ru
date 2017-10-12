@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        You have successfully registered in <a href="{{config('app.url')}}">{{config('app.name')}}</a> - CME Group commodity analysis system.
    </p>
    <p>
        Login: <b>{{$email}}</b>
    </p>
    <p>
        Password: <b>{{$password}}</b>
    </p>
    <p>
        Thank you for registering, and hope that {{config('app.name')}} will be useful and convenient in use.
    </p>
@stop