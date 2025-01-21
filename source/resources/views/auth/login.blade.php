<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <title>Менеджер прайс-листов</title>
    </head>
    <body>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="true" href="{{ route('login') }}">Вход в систему</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('get-register') }}">Регистрация нового пользователя</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('password.reset') }}">Сброс пароля</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('post-login') }}">
                                @csrf

                                <div class="form-group row mb-4">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">Адресс электронной почты</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="" required>
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="password" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="remember" class="col-md-4 col-form-label text-md-right">Запомнить</label>

                                    <div class="col-md-6">
                                        <input id="remember" type="checkbox" name="remember"{{ old('remember', false) ? ' checked' : '' }}>
                                    </div>
                                </div>


                                <div class="form-group row mb-4">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Войти
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>