@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        You requested a change of your e-mail on the website <a href="{{config('app.url')}}">{{config('app.name')}}</a>! If you did not do this, then just ignore this message.
    </p>
    <p>
        To change e-mail, you need to go through this <a href="{{config('app.url')}}/new/email/{{$token}}" target="_blank">link</a>.
    </p>
@stop