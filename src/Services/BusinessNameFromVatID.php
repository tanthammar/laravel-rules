<?php

namespace TantHammar\LaravelRules\Services;

use Mpociot\VatCalculator\Facades\VatCalculator;

class BusinessNameFromVatID
{
    public static function lookup(string $vatID): string
    {
        $unknown = trans('laravel-rules::msg.vat-name-unknown');

        if (! $vatID) {
            return $unknown;
        }

        try {
            return VatCalculator::getVATDetails($vatID)?->name ?? $unknown;
        } catch (\Exception) {
            return $unknown;
        }
    }
}
