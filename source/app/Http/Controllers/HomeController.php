<?php

namespace App\Http\Controllers;

use App\Models\RequestModel;

class HomeController extends Controller
{
    public function index()
    {
        $completedRequests = [];
        foreach (RequestModel::orderByDesc('created_at')->get() as $request) {
            $completedRequests[] = [
                'uuid' => $request->uuid,
                'result' => $request->result,
                'status' => $request->status,
                'stats' => json_decode($request->stats, true),
                'created_at' => $request->created_at->setTimezone('US/Eastern')->format("Y-m-d H:m:s"),
            ];
        }

        return view('index', ['completedRequests' => $completedRequests]);
    }
}
