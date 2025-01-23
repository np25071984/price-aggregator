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
                            Подтвердите свой адрес электронной почты
                        </div>
                        <div class="card-body">
                            @if (session()->has('message'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('message') }}
                                </div>
                            @endif

                            <p>Мы отправили вам электронное письмо с инструкцией для подтверждения вашего адреса электронной почты.</p>

                            <a href="{{ route('verification.send') }}">Выслать повторно</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>


