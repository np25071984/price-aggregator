<?php

namespace App\Jobs;

use App\Converters\PriceIdConverter;
use App\Converters\Merge\ConverterFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\DirectoryReader;
use App\Enums\PriceListProviderEnum;
use App\Enums\RequestStatusEnum;
use App\Exceptions\UnknownFileException;
use App\FileMergeWriter;
use App\Models\RequestModel;

class MergePriceListsJob implements ShouldQueue
{
    use Queueable;

    private const OUTPUT_FILE_NAME = 'merged.xlsx';

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $requestId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storagePath = storage_path("app/public/{$this->requestId}");

        $directoryReader = new DirectoryReader($storagePath);
        $priceIdConverter = new PriceIdConverter();
        $converterFactory = new ConverterFactory();
        $data = [];
        $requestModel = RequestModel::findOrFail($this->requestId);
        $requestModel->status = RequestStatusEnum::Processing->value;
        $requestModel->save();

        foreach ($directoryReader->read(["xlsx", "xls"]) as $filePathName => $extension) {
            $fileName = basename($filePathName);
            $reader = IOFactory::createReader($extension);
            $spreadsheet = $reader->load($filePathName);
            try {
                $converter = $converterFactory->determineConverter($spreadsheet);
            } catch (UnknownFileException $e) {
                // TODO: log this
                $stats[$fileName]['id'] = 'Не распознан';
                $stats[$fileName]['count'] = 0; // TODO: count items
                $requestModel->stats = json_encode($stats);
                $requestModel->save();

                continue;
            }
            $rawPriceData = $converter->convert($spreadsheet);

            $stats = json_decode($requestModel->stats, true);
            $stats[$fileName]['id'] = $priceIdConverter->convert($converter->getPriceId());
            $stats[$fileName]['count'] = array_sum(
                array_map(
                    fn ($itemsArray) => count($itemsArray),
                    $rawPriceData
                )
            );
            $requestModel->stats = json_encode($stats);
            $requestModel->save();

            $data = $data + $rawPriceData;
        }

        ksort($data);

        $writer = new FileMergeWriter();
        $writer->save(sprintf("{$storagePath}/%s", self::OUTPUT_FILE_NAME), $data);

        $requestModel->status = RequestStatusEnum::Finished->value;
        $requestModel->result = self::OUTPUT_FILE_NAME;
        $requestModel->save();
    }
}
