<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;
use App\Validators\AvangardUsdValidator;
use App\Validators\BeliyUsdValidator;
use App\Validators\DePerfumesUsdValidator;
use App\Validators\GevorgUsdValidator;
use App\Validators\GuldenRuValidator;
use App\Validators\KurzinaUsdValidator;
use App\Validators\NashaFirmaUsdValidator;
use App\Validators\NichePerfumeUsdValidator;
use App\Validators\OrabelUsdValidator;
use App\Validators\RagimovaDianaUsdValidator;
use App\Validators\StockUsdValidator;
use App\Validators\ZurabUsdValidator;

class PriceListIdentifier
{
    public function identiry(Spreadsheet $spreadsheet): PriceListProviderEnum
    {
        $priceId = match (true) {
            (new DePerfumesUsdValidator)($spreadsheet) => PriceListProviderEnum::DePerfumesUsd,
            (new NichePerfumeUsdValidator)($spreadsheet) => PriceListProviderEnum::NichePerfumeUsd,
            (new AvangardUsdValidator)($spreadsheet) => PriceListProviderEnum::AvangardUsd,
            (new BeliyUsdValidator)($spreadsheet) => PriceListProviderEnum::BeliyUsd,
            (new GevorgUsdValidator)($spreadsheet) => PriceListProviderEnum::GevorgUsd,
            (new GuldenRuValidator)($spreadsheet) => PriceListProviderEnum::GuldenRu,
            (new ZurabUsdValidator)($spreadsheet) => PriceListProviderEnum::ZurabUsd,
            (new NashaFirmaUsdValidator)($spreadsheet) => PriceListProviderEnum::NashaFirmaUsd,
            (new OrabelUsdValidator)($spreadsheet) => PriceListProviderEnum::OrabelUsd,
            (new RagimovaDianaUsdValidator)($spreadsheet) => PriceListProviderEnum::RagimovaDianaUsd,
            (new KurzinaUsdValidator)($spreadsheet) => PriceListProviderEnum::KurzinaUsd,
            (new StockUsdValidator)($spreadsheet) => PriceListProviderEnum::StockUsd,
            default => PriceListProviderEnum::Unknown,
        };

        return $priceId;
    }
}