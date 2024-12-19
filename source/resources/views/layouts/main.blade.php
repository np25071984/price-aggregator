<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <title>@yield('title') - Менеджер прайс-листов</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Менеджер прайс-листов</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a {!! Route::currentRouteName() === 'get-aggregation' ? 'class="nav-link active"' : 'class="nav-link"' !!} aria-current="page" href="/aggregation">Аггрегация</a>
                        </li>
                        <li class="nav-item">
                            <a {!! Route::currentRouteName() === 'get-merge' ? 'class="nav-link active"' : 'class="nav-link"' !!} href="/merge">Слияние</a>
                        </li>
                    </ul>
                    <span class="navbar-text">
                        v{{ config('app.version') }}
                    </span>
                </div>
            </div>
        </nav>

        @yield('content')

    </body>
</html>