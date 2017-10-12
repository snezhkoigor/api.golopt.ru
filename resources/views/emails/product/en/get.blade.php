@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        Your request for {{ $is_demo ? 'demo product version' : 'product' }} "{{ $product['name'] }}" successfully processed on the website <a href="{{config('app.url')}}">{{config('app.name')}}</a>.
    </p>
    <p>
        You can download it in your personal account.
    </p>
    <p>
        Upon completion of access, you will receive a notification.
    </p>
@stop