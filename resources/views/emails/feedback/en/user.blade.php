@extends('emails.layouts.plain')

@section('content')
    Hi!

    Your message on the site <a href="{{config('app.url')}}">{{ config('app.name') }}</a> received. We will answer to you.
@stop