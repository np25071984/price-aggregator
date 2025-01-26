<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Models\RequestModel;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function aggregation()
    {
        $completedRequests = [];
        $existingRequests = RequestModel::where([
                'user_id' => Auth::user()->id,
                'type' => RequestTypeEnum::Aggregation->value,
            ])->
            orderByDesc('created_at')->
            get();

        foreach ($existingRequests as $request) {
            $completedRequests[] = [
                'uuid' => $request->uuid,
                'result' => $request->result,
                'status' => $this->mapStatusToText($request->status),
                'stats' => json_decode($request->stats, true),
                'created_at' => $request->
                    created_at->
                    locale(config('app.locale'))->
                    setTimezone('Europe/Moscow')->
                    translatedFormat("j F Y, H:i:s"),
            ];
        }

        return view('aggregation', ['completedRequests' => $completedRequests]);
    }

    public function merge()
    {
        $completedRequests = [];
        $existingRequests = RequestModel::where([
                'user_id' => Auth::user()->id,
                'type' => RequestTypeEnum::Merge->value
            ])->
            orderByDesc('created_at')->
            get();
        foreach ($existingRequests as $request) {
            $completedRequests[] = [
                'uuid' => $request->uuid,
                'result' => $request->result,
                'status' => $this->mapStatusToText($request->status),
                'stats' => json_decode($request->stats, true),
                'created_at' => $request->
                    created_at->
                    locale(config('app.locale'))->
                    setTimezone('Europe/Moscow')->
                    translatedFormat("j F Y, H:i:s"),
            ];
        }

        return view('merge', ['completedRequests' => $completedRequests]);
    }

    private function mapStatusToText(RequestStatusEnum $status): string
    {
        return match($status) {
            RequestStatusEnum::Uploading => 'Загрузка',
            RequestStatusEnum::Pending => 'Ожидание обработки',
            RequestStatusEnum::Processing => 'Идет обработка',
            RequestStatusEnum::Finished => 'Обработан',
        };
    }
}
