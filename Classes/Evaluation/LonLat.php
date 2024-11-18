<?php

namespace Bobosch\OdsOsm\Evaluation;

class LonLat
{
    public function returnFieldJS(): string
    {
        return "return value;";
    }

    public function evaluateFieldValue(string $value): string
    {
        return "1.1";
         // test if we have any value
         if ($value && $value !== '') {
            return sprintf('%01.6f', $value);
        }
        return null;
    }
}
