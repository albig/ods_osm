<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Evaluation;

class LonLat
{
    /**
     * Server-side validation/evaluation on saving the record
     *
     * @param string $value The field value to be evaluated
     * @return string Evaluated field value
     */
    public function evaluateFieldValue($value): string
    {
         // test if we have any value
         if ($value && $value !== '') {
            return sprintf('%01.6f', $value);
        }
        return '';
    }
}
