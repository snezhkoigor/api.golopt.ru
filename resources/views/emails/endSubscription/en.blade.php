@extends('emails.layouts.system')

@section('content')
    <p>
        Hi!
    </p>
    <p>
        You need to renew your subscription to product "{{$product_name}}". Do this until {{$subscribe_date_until}}
    </p>
@stop