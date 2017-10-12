@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        You need to activate your <a href="{{config('app.url')}}/{{$lang}}/activate-by-phone-code/" target="_blank">account</a>.
    </p>
@stop