@extends('email.layouts.custom')

@section('content')
    <h1 style="color: #1f2937; font-size: 24px; margin-bottom: 20px;">Сброс пароля</h1>

    <p style="color: #4b5563; margin-bottom: 20px;">
        Вы видите это электронное письмо т.к. мы получили запрос на смену пароля от вашего аккаунта.
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $url }}" class="button">
            Сбросить пароль
        </a>
    </div>

    <p style="color: #4b5563; margin-bottom: 20px;">
        Это ссылка на сброс пароля истекает через {{ $expire_min }} минут.
    </p>

    <p style="color: #6b7280; font-size: 14px;">
        Если вы не запрашивали смену пароля, то просто игнорируйте это письмо.
    </p>
@endsection