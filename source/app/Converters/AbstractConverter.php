<?php

namespace App\Converters;

abstract readonly class AbstractConverter implements ConverterInterface
{
    protected function normolizeString(string $string): string
    {
        $string = mb_strtolower($string);

        /**
         * little data hacks
         */
        $string = mb_ereg_replace(preg_quote(" ", "/"), " ", $string);
        $string = preg_replace('/\s{2,}/', " ", $string);
        $string = trim($string);

        return $this->fixData($string);
    }

    protected function getFixes(): array
    {
        return [];
    }

    private function fixData(string $string): string
    {
        foreach($this->getFixes() as $regExp => $fixedValue) {
            $string = mb_ereg_replace($regExp, $fixedValue, $string);
        }

        return $string;
    }
}