@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Ваш e-mail адрес был успешно изменен на сайте {{config('app.name')}}.
    </p>
@stop