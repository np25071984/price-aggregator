@extends('layouts.main')

@section('title')
Аггрегация прайс-листов
@endsection

@section('content')
<div class="container">
    <div class="row">
        <h1 class="mt-5 text-center">Новый запрос на аггрегацию</h1>
    </div>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endforeach
    @endif
    <form class="clearfix" enctype="multipart/form-data" method="POST" action="/upload" onsubmit="submitButton.disabled = true; return true;">
        @csrf
        <input type="hidden" name="requestType" value="aggregation">
        <div class="mb-3">
            <label for="files" class="form-label">Выбиерите прайс-листы для аггрегации:</label>
            <input class="form-control" type="file" id="files" name="files[]" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" multiple>
        </div>

        <button name="submitButton" type="submit" class="btn btn-primary float-end">Отправить на аггрегацию</button>
    </form>

    <div class="row">
        <h1 class="mt-5 text-center">Обработанные запросы</h1>
    </div>

    @include('accordion', ['completedRequests' => $completedRequests])

</div>
@endsection
