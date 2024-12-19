<?php

namespace App\Converters\Aggregate;

use App\Exceptions\UnknownFileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Validators\AvangardUsdValidator;
use App\Validators\BeliyUsdValidator;
use App\Validators\DePerfumesUsdValidator;
use App\Validators\GevorgUsdValidator;
use App\Validators\GuldenRubValidator;
use App\Validators\KurzinaUsdValidator;
use App\Validators\NashaFirmaUsdValidator;
use App\Validators\NichePerfumeUsdValidator;
use App\Validators\OrabelUsdValidator;
use App\Validators\RagimovaDianaUsdValidator;
use App\Validators\StockUsdValidator;
use App\Validators\ZurabUsdValidator;

class ConverterFactory
{
    public function determineConverter(Spreadsheet $spreadsheet): AbstractConverter
    {
        return match (true) {
            (new DePerfumesUsdValidator)($spreadsheet) => new DePerfumesConverter,
            (new NichePerfumeUsdValidator)($spreadsheet) => new NichePerfumeUsdConverter,
            (new AvangardUsdValidator)($spreadsheet) => new AvangardUsdConverter,
            (new BeliyUsdValidator)($spreadsheet) => new BeliyUsdConverter,
            (new GevorgUsdValidator)($spreadsheet) => new GevorgUsdConverter,
            (new GuldenRubValidator)($spreadsheet) => new GuldenRubConverter,
            (new ZurabUsdValidator)($spreadsheet) => new ZurabUsdConverter,
            (new NashaFirmaUsdValidator)($spreadsheet) => new NashaFirmaUsdConverter,
            (new OrabelUsdValidator)($spreadsheet) => new OrabelUsdConverter,
            (new RagimovaDianaUsdValidator)($spreadsheet) => new RagimovaDianaUsdConverter,
            (new KurzinaUsdValidator)($spreadsheet) => new KurzinaUsdConverter,
            (new StockUsdValidator)($spreadsheet) => new StockUsdConverter,
            default => throw new UnknownFileException("Couldn't determine converter"),
        };
    }
}