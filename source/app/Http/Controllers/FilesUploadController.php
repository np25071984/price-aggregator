<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFilesRequest;
use \Illuminate\Http\UploadedFile;
use App\Jobs\MergePriceListsJob;

class FilesUploadController extends Controller
{
    public function upload(UploadFilesRequest $request)
    {
        $mergeId = uniqid(rand());
        $files = $request->file("files");
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $file->storeAs("./{$mergeId}", $fileName, ['disk' => 'public']);
        }
        MergePriceListsJob::dispatchSync($mergeId);

        return redirect("/storage/{$mergeId}/combined.xlsx");
    }
}
