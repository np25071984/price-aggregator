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
            <div class="row float-end">
                <small class="align-end">v{{ config('app.version') }}</small>
            </div>
            <div class="row">
                <h1 class="mt-5 text-center">Новый запрос</h1>
            </div>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">{{ $error }}</div>
                @endforeach
            @endif
            <form class="clearfix" enctype="multipart/form-data" method="POST" action="/upload" onsubmit="submitButton.disabled = true; return true;">
                @csrf
                <div class="mb-3">
                    <label for="files" class="form-label">Выбиерите прайс-листы для аггрегации:</label>
                    <input class="form-control" type="file" id="files" name="files[]" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" multiple>
                </div>

                <button name="submitButton" type="submit" class="btn btn-primary float-end">Отправить на аггрегацию</button>
            </form>


            <div class="row">
                <h1 class="mt-5 text-center">Обработанные запросы</h1>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2" scope="col">#</th>
                            <th class="text-center" rowspan="2" scope="col">Итоговый файл</th>
                            <th class="text-center" rowspan="2" scope="col">Дата</th>
                            <th class="text-center" rowspan="2" scope="col">Статус</th>
                            <th class="text-center" scope="col" colspan="3">Статистика</th>
                        </tr>
                        <tr>
                            <th class="text-center">Загруженный файл</th>
                            <th class="text-center">Код поставщика</th>
                            <th class="text-center">Кол-во загруженных товаров</th>
                        </tr>
                    </thead>
                        <tbody>
                        @foreach ($completedRequests as $key => $request)
                            <?php $statRowCnt = count($request['stats']) ?>
                            <tr>
                                <td <?php echo ($statRowCnt > 0) ? "rowspan='{$statRowCnt}'" : '' ?> scope="row" class="text-center">{{ $key + 1 }}</th>
                                <td <?php echo ($statRowCnt > 0) ? "rowspan='{$statRowCnt}'" : '' ?>>
                                    @if (!empty($request['result']))
                                    <a href="<?php echo "/storage/{$request['uuid']}/{$request['result']}"; ?>">{{ $request['result'] }}</a>
                                    @endif
                                </td>
                                <td <?php echo ($statRowCnt > 0) ? "rowspan='{$statRowCnt}'" : '' ?> class="text-center">{{ $request['created_at'] }}</td>
                                <td <?php echo ($statRowCnt > 0) ? "rowspan='{$statRowCnt}'" : '' ?> class="text-center">{{ $request['status'] }}</td>
                                @if ($statRowCnt > 0)
                                    <?php $i = 0 ?>
                                    @foreach ($request['stats'] as $file => $stat)
                                        @if ($i !== 0)
                                            </tr><tr>
                                        @endif
                                        <td>
                                        @if (!empty($file))
                                            <a href="<?php echo "/storage/{$request['uuid']}/{$file}"; ?>">{{ $file }}</a>
                                        @endif
                                        </td>
                                        <td class="text-center">{{ $stat['id'] }}</td>
                                        <td class="text-center">{{ $stat['count'] }}</td>
                                        <?php $i++ ?>
                                    @endforeach
                                @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </body>
</html>