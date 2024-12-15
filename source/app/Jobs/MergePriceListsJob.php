<?php

namespace App\Jobs;

use App\Converters\ConverterFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\DirectoryReader;
use App\FileWriter;
use App\DataAnalizer;
use App\Exceptions\UnknownFileException;

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
        $converterFactory = new ConverterFactory();
        $dataAnalizer = new DataAnalizer();
        $data = [];
        $filesStatus = [];
        foreach ($directoryReader->read(["xlsx", "xls"]) as $filePathName => $extension) {
            $fileName = basename($filePathName);
            $filesStatus[$fileName] = [];
            $reader = IOFactory::createReader($extension);
            $spreadsheet = $reader->load($filePathName);
            try {
                $converter = $converterFactory->determineConverter($spreadsheet);
            } catch (UnknownFileException $e) {
                $filesStatus[$fileName]['items_count'] = 0;
                // TODO: log this
                continue;
            }
            $rawPriceData = $converter->convert($spreadsheet);
            $filesStatus[$fileName]['id'] = $converter->getPriceId()->value;
            $filesStatus[$fileName]['items_count'] = count($rawPriceData);
            $data = array_merge(
                $data,
                $dataAnalizer->analyze($rawPriceData, $converter->getPriceId())
            );
        }

        $writer = new FileWriter();
        $writer->save("{$storagePath}/combined.xlsx", $data, $filesStatus);
    }
}
