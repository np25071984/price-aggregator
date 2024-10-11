<?php

namespace App\Converters;

abstract readonly class AbstractConverter implements ConverterInterface
{
    protected function normolizeString(string $string): string
    {
        $string = mb_strtolower($string);

        /**
         * little data hacks
         * TODO: possible multibyte issue; replace str_replace function
         */
        $string = str_replace(" ", " ", $string);
        $string = preg_replace('/\s{2,}/', " ", $string);

        return trim($string);
    }
}