<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFilesRequest;
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
            'status' => 'uploading',
        ]);

        // delete previous requests
        $oldRequests = RequestModel::orderByDesc('created_at')->skip(3)->get();
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
        $requestModel->status = 'pending';
        $requestModel->save();

        MergePriceListsJob::dispatchSync($requestModel->uuid);

        return redirect()->route('index');;
    }
}
