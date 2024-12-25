<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BrandAliasModel;
use App\Models\BrandModel;
use Illuminate\Support\Facades\DB;

class FillDictionaries extends Command
{
    private const OUTPUT_WIDTH = 80;
    protected $signature = 'app:fill-dicts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill db tables with data from php files';

    private array $brands = [];
    private array $brandStopPhrases = [];

    public function __construct()
    {
        parent::__construct();

        $this->brands = include __DIR__ . "/../../../dictionaries/brands.php";
        $this->brandStopPhrases = include __DIR__ . "/../../../dictionaries/brandStopPhrases.php";
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach ($this->brands as $alias => $brandName) {
            $msg = "Looking for brand '{$brandName}'";
            $brandModel = BrandModel::firstOrCreate([
                'name' => $brandName,
            ]);

            $msg = "Looking for stop-phrases";
            if (isset($this->brandStopPhrases[$brandName])) {
                $quotedBrandStopPhrases = array_map(
                    fn($s) => "'{$s}'",
                    $this->brandStopPhrases[$brandName]
                );
                $stopWords = DB::raw('ARRAY[' . implode(',', $quotedBrandStopPhrases) . ']');
                $this->printScreenWide($msg, '[found]');
            } else {
                $stopWords = null;
                $this->printScreenWide($msg, '[not found]');
            }

            $msg = "Creating alias: '{$alias}'";
            BrandAliasModel::firstOrCreate([
                'brand_id' => $brandModel->id,
                'alias' => $alias,
            ], [
                'size' => mb_strlen($alias),
                'stop-words' => $stopWords,
            ]);
            $this->printScreenWide($msg, '[ok]');
        }

        $this->info("done");
    }

    private function printScreenWide(string $message, string $status): void
    {
        $filler = str_repeat('.', self::OUTPUT_WIDTH - mb_strlen($message) - mb_strlen($status));
        $this->info($message . $filler . $status);
    }

}