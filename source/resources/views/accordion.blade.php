<div class="row">
    <div class="accordion" id="accordionExample">
        @foreach ($completedRequests as $i => $request)
            <?php $statRowCnt = count($request['stats']) ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button<?php echo ($i === 0) ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i }}" aria-expanded="<?php echo ($i === 0) ? 'true' : 'false' ?>" aria-controls="collapse-{{ $i }}">
                        {{ $request['created_at'] }} ({{ $request['status'] }})
                    </button>
                </h2>
                <div id="collapse-{{ $i }}" class="accordion-collapse collapse<?php echo ($i === 0) ? " show" : '' ?>" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h4>Итоговый файл:
                    @if (!empty($request['result']))
                        <a href="<?php echo "/storage/{$request['uuid']}/{$request['result']}"; ?>">{{ $request['result'] }}</a>
                    @endif
                    </h4>
                    <br />

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">Загруженный файл</th>
                                <th class="text-center">Код поставщика</th>
                                <th class="text-center">Кол-во загруженных товаров</th>
                            </tr>
                        </thead>
                            <tbody>
                                @foreach ($request['stats'] as $file => $stat)
                                <tr>
                                    <td>
                                    @if (!empty($file))
                                        <a href="<?php echo "/storage/{$request['uuid']}/{$file}"; ?>">{{ $file }}</a>
                                    @endif
                                    </td>
                                    <td class="text-center">{{ $stat['id'] }}</td>
                                    <td class="text-center">{{ $stat['count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                    </table>
                </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
