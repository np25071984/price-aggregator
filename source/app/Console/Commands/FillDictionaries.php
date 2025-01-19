<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BrandAliasModel;
use App\Models\BrandModel;
use App\Models\TitleAliasModel;
use App\Models\TitleModel;
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
    private array $titles = [];

    public function __construct()
    {
        parent::__construct();

        $this->brands = include __DIR__ . "/../../../dictionaries/brands.php";
        $this->brandStopPhrases = include __DIR__ . "/../../../dictionaries/brandStopPhrases.php";
        $this->titles = include __DIR__ . "/../../../dictionaries/names.php";
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach ($this->brands as $alias => $brandName) {
            $msg = "Creating brand: '{$brandName}'";
            $brandModel = BrandModel::firstOrCreate([
                'name' => $brandName,
            ]);
            $this->info($this->getScreenWide($msg, '[ok]'));

            $msg = "    Filling stop-phrases: ";
            if (isset($this->brandStopPhrases[$brandName])) {
                $quotedBrandStopPhrases = array_map(
                    fn($s) => "'{$s}'",
                    $this->brandStopPhrases[$brandName]
                );
                $stopWords = DB::raw('ARRAY[' . implode(',', $quotedBrandStopPhrases) . ']');
                $this->info($this->getScreenWide($msg . implode(",", $quotedBrandStopPhrases), '[found]'));
            } else {
                $this->info($this->getScreenWide($msg, '[not found]'));
                $stopWords = null;
            }

            $msg = "    Creating alias: '{$alias}'";
            BrandAliasModel::firstOrCreate([
                'brand_id' => $brandModel->id,
                'alias' => $alias,
            ], [
                'size' => mb_strlen($alias),
                'stop-words' => $stopWords,
            ]);
            $this->info($this->getScreenWide($msg, '[ok]'));

            $msg = "    Filling titles: ";
            if (!isset($this->titles[$brandName])) {
                $this->error($this->getScreenWide($msg, "[not found]"));
            }

            foreach ($this->titles[$brandName] ?? [] as $alias => $title) {
                $msg = "        Creating title: '{$title}'";
                $titleModel = TitleModel::firstOrCreate([
                    'brand_id' => $brandModel->id,
                    'title' => $title,
                ]);
                $this->info($this->getScreenWide($msg, '[ok]'));


                $msg = "            Creating title-alias: '{$alias}'";
                TitleAliasModel::firstOrCreate([
                    'title_id' => $titleModel->id,
                    'alias' => $alias,
                ], [
                    'size' => mb_strlen($alias),
                ]);
                $this->info($this->getScreenWide($msg, '[ok]'));
            }
        }

        $this->info("done");
    }

    private function getScreenWide(string $message, string $status): string
    {
        return $message .
            str_repeat('.', max(self::OUTPUT_WIDTH - mb_strlen($message) - mb_strlen($status), 0)) .
            $status;
    }

}