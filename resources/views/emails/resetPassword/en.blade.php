@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        Password: <b>{{$password}}</b>
    </p>
    <p>
        You can always change it in your personal account.
    </p>
@stop