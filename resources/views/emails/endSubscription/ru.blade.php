@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Вам необходимо продлить подписку на "{{strtoupper($product_group)}}: {{$product_name}}". Она заканчивается {{$subscribe_date_until}}
    </p>
@stop