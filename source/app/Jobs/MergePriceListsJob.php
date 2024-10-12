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

class MergePriceListsJob implements ShouldQueue
{
    use Queueable;

    private string $mergeId;
    private array $brands;
    private array $brandStopPhrases;
    private array $volumes;
    private array $types;
    private array $testerFlags;
    private array $sampleFlags;
    private array $brandLines;
    private array $brandSets;
    private array $oldDesignFlags;
    private array $artisanalBottlingFlags;
    private array $markingFlags;
    private array $sex;
    private array $damageFlags;
    private array $refillFlags;
    private array $fillerWords;


    /**
     * Create a new job instance.
     */
    public function __construct(string $mergeId)
    {
        ini_set('memory_limit', '512M');

        $this->mergeId = $mergeId;

        // we want to sort all associative dictionaries by key length to avoid false hits
        $brands = include __DIR__ . "/../../dictionaries/brands.php";
        uksort($brands, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });
        $this->brands = $brands;

        $brandLines = include __DIR__ . "/../../dictionaries/brandLines.php";
        $brandLinesSorted = [];
        foreach($brandLines as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandLinesSorted[$brand] = $items;
        }
        unset($brandLines);
        $this->brandLines = $brandLinesSorted;

        $brandSets = include __DIR__ . "/../../dictionaries/brandSets.php";
        $brandSetsSorted = [];
        foreach($brandSets as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandSetsSorted[$brand] = $items;
        }
        unset($brandSets);
        $this->brandSets = $brandSetsSorted;

        $this->brandStopPhrases = include __DIR__ . "/../../dictionaries/brandStopPhrases.php";
        $this->volumes = include __DIR__ . "/../../dictionaries/volumes.php";
        $this->types = include __DIR__ . "/../../dictionaries/types.php";
        $this->testerFlags = include __DIR__ . "/../../dictionaries/testerFlags.php";
        $this->sampleFlags = include __DIR__ . "/../../dictionaries/sampleFlags.php";
        $this->oldDesignFlags = include __DIR__ . "/../../dictionaries/oldDesignFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../../dictionaries/markingFlags.php";
        $this->sex = include __DIR__ . "/../../dictionaries/sex.php";
        $this->damageFlags = include __DIR__ . "/../../dictionaries/damageFlags.php";
        $this->refillFlags = include __DIR__ . "/../../dictionaries/refillFlags.php";
        $this->fillerWords = include __DIR__ . "/../../dictionaries/fillerWords.php";
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
        $data = [];
        foreach ($directoryReader->read(["xlsx", "xls"]) as $filePathName => $extension) {
            $reader = IOFactory::createReader($extension);
            $spreadsheet = $reader->load($filePathName);
            $priceId = $priceListIdentifier->identiry($spreadsheet);
            if ($priceId === PriceListProviderEnum::Unknown) {
                // TODO: log this
                continue;
            }
            $converter = $priceListConverterFactory->getConverter($priceId);
            $data = array_merge($data, $converter->convert($spreadsheet, basename($filePathName)));
        }

        $writer = new FileWriter();
        $writer->save("{$storagePath}/combined.xlsx", $data);
    }
}
