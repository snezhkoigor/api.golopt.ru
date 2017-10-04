@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Вам необходимо активировать свой <a href="{{config('app.url')}}/{{ $lang }}/activate-by-phone-code/" target="_blank">аккаунт</a>.
    </p>
@stop