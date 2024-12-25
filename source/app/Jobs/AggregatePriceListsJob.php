<?php

namespace App\Jobs;

use App\Converters\Aggregate\ConverterFactory;
use App\Converters\PriceIdConverter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\DirectoryReader;
use App\DataAnalizer;
use App\Enums\PriceListProviderEnum;
use App\Enums\RequestStatusEnum;
use App\Exceptions\UnknownFileException;
use App\FileAggregateWriter;
use App\Models\RequestModel;

class AggregatePriceListsJob implements ShouldQueue
{
    use Queueable;

    private const OUTPUT_FILE_NAME = 'combined.xlsx';

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
        $dataAnalizer = new DataAnalizer();
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
                $stats[$fileName]['count'] = 0;
                $requestModel->stats = json_encode($stats);
                $requestModel->save();

                continue;
            }
            $rawPriceData = $converter->convert($spreadsheet);

            $stats = json_decode($requestModel->stats, true);
            $stats[$fileName]['id'] = $priceIdConverter->convert($converter->getPriceId());
            $stats[$fileName]['count'] = count($rawPriceData);
            $requestModel->stats = json_encode($stats);
            $requestModel->save();

            $data = array_merge(
                $data,
                $dataAnalizer->analyze($rawPriceData, $converter->getPriceId())
            );
        }
        $writer = new FileAggregateWriter($priceIdConverter);
        $writer->save(sprintf("{$storagePath}/%s", self::OUTPUT_FILE_NAME), $data);

        $requestModel->status = RequestStatusEnum::Finished->value;
        $requestModel->result = self::OUTPUT_FILE_NAME;
        $requestModel->save();
    }
}
