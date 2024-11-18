<?php

namespace Bobosch\OdsOsm\Evaluation;

class LonLat
{
    public function evaluateFieldValue(string $value): string
    {
         // test if we have any value
         if ($value && $value !== '') {
            return sprintf('%01.6f', $value);
        }
        return '';
    }
}
