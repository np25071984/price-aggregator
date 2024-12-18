<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Http\Requests\UploadFilesRequest;
use App\Jobs\AggregatePriceListsJob;
use \Illuminate\Http\UploadedFile;
use App\Jobs\MergePriceListsJob;
use App\Models\RequestModel;
use Illuminate\Support\Facades\Storage;
class FilesUploadController extends Controller
{
    public function upload(UploadFilesRequest $request)
    {
        $uploadedFiles = [];
        $stats = [];

        $requestModel = RequestModel::create([
            'result' => '',
            'type' => $request->requestType->value,
            'status' => RequestStatusEnum::Uploading->value,
        ]);

        // delete previous requests
        $oldRequests = RequestModel::where(['type' => $request->requestType->value])->
            orderByDesc('created_at')->
            skip(3)->
            get();
        foreach ($oldRequests as $oldRequest) {
            RequestModel::where(['uuid' => $oldRequest->uuid])->delete();
            Storage::disk('public')->deleteDirectory($oldRequest->uuid);
        }

        $files = $request->file("files");
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $uploadedFiles[] = $fileName;
            $file->storeAs("./{$requestModel->uuid}", $fileName, ['disk' => 'public']);
            $stats[$fileName] = [
                'id' => '',
                'count' => 0,
            ];
        }

        $requestModel->stats = json_encode($stats);
        $requestModel->status = RequestStatusEnum::Pending->value;
        $requestModel->save();

        $redirectRoute = null;
        switch ($request->requestType) {
            case RequestTypeEnum::Aggregation:
                AggregatePriceListsJob::dispatchSync($requestModel->uuid);
                $redirectRoute = 'get-aggregation';
                break;
            case RequestTypeEnum::Merge:
                MergePriceListsJob::dispatchSync($requestModel->uuid);
                $redirectRoute = 'get-merge';
                break;
        };

        return redirect()->route($redirectRoute);
    }
}
