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

        file_put_contents("/tmp/price-aggregator.txt", "redirect to: /storage/{$mergeId}/combined.xlsx" . PHP_EOL, FILE_APPEND);
        return redirect("/storage/{$mergeId}/combined.xlsx");
    }
}
