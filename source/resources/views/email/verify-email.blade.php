@extends('email.layouts.custom')

@section('content')
    <h1 style="color: #1f2937; font-size: 24px; margin-bottom: 20px;">Подтверждение адреса электронной почты</h1>

    <p style="color: #4b5563; margin-bottom: 20px;">
        Для завершения регистрации вам необходимо подтвердить адрес электронной почты, перейдя по ссылке ниже
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $url }}" class="button">
            Подтвердить адрес
        </a>
    </div>

    <p style="color: #4b5563; margin-bottom: 20px;">
        Эта ссылка дейтвительна до {{ $expire }}.
    </p>

@endsection