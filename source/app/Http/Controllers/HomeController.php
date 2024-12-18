<?php

namespace App\Http\Controllers;

use App\Enums\RequestTypeEnum;
use App\Models\RequestModel;

class HomeController extends Controller
{
    public function aggregation()
    {
        $completedRequests = [];
        $existingRequests = RequestModel::where(['type' => RequestTypeEnum::Aggregation->value])->
            orderByDesc('created_at')->
            get();

        foreach ($existingRequests as $request) {
            $completedRequests[] = [
                'uuid' => $request->uuid,
                'result' => $request->result,
                'status' => $request->status->value,
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
        $existingRequests = RequestModel::where(['type' => RequestTypeEnum::Merge->value])->
            orderByDesc('created_at')->
            get();
        foreach ($existingRequests as $request) {
            $completedRequests[] = [
                'uuid' => $request->uuid,
                'result' => $request->result,
                'status' => $request->status->value,
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
}
