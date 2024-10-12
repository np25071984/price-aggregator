<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <title>Слияние прайс-листов</title>
    </head>
    <body>
        <div class="container">
            <h1 class="text-center mt-5">Слияние прайс-листов</h1>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">{{ $error }}</div>
                @endforeach
            @endif
            <form enctype="multipart/form-data" method="POST" action="/upload">
                @csrf
                <div class="mb-3">
                    <label for="files" class="form-label">Выбиерите прайс-листы:</label>
                    <input class="form-control" type="file" id="files" name="files[]" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" multiple>
                </div>

                <button type="submit" class="btn btn-primary">Отправить на слияние</button>
            </form>
        </div>
    </body>
</html>