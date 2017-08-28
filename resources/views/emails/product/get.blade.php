@extends('emails.layouts.system')

@section('content')
    <p>
        Здравствуйте!
    </p>
    <p>
        Ваш запрос на {{ $is_demo ? 'демо версию продукта' : 'продукт' }} "{{ $product['name'] }}" успешно обработан на сайте {{config('app.name')}}.
    </p>
    <p>
        Скачать его вы можете пройдя по <a href="{{ $product['path'] }}" target="_blank">ссылке</a>
    </p>
    <p>
        По завершению доступа, вы получите уведомление.
    </p>
@stop