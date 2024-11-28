<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\DirectoryReader;
use App\PriceListIdentifier;
use App\Converters\PriceListConverterFactory;
use App\FileWriter;
use App\Enums\PriceListProviderEnum;
use App\DataAnalizer;

class MergePriceListsJob implements ShouldQueue
{
    use Queueable;

    private string $mergeId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $mergeId)
    {
        $this->mergeId = $mergeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storagePath = storage_path("app/public/{$this->mergeId}");

        $directoryReader = new DirectoryReader($storagePath);
        $priceListIdentifier = new PriceListIdentifier();
        $priceListConverterFactory = new PriceListConverterFactory();
        $dataAnalizer = new DataAnalizer();
        $data = [];
        $filesStatus = [];
        foreach ($directoryReader->read(["xlsx", "xls"]) as $filePathName => $extension) {
            $fileName = basename($filePathName);
            $filesStatus[$fileName] = [];
            $reader = IOFactory::createReader($extension);
            $spreadsheet = $reader->load($filePathName);
            $priceId = $priceListIdentifier->identiry($spreadsheet);
            $filesStatus[$fileName]['id'] = $priceId->value;
            if ($priceId === PriceListProviderEnum::Unknown) {
                $filesStatus[$fileName]['items_count'] = 0;
                // TODO: log this
                continue;
            }
            $converter = $priceListConverterFactory->getConverter($priceId);
            $rawPriceData = $converter->convert($spreadsheet);
            $filesStatus[$fileName]['items_count'] = count($rawPriceData);
            $data = array_merge($data, $dataAnalizer->analyze($rawPriceData, $priceId));
        }

        $writer = new FileWriter();
        $writer->save("{$storagePath}/combined.xlsx", $data, $filesStatus);
    }
}
