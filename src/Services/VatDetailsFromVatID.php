<?php

namespace TantHammar\LaravelRules\Services;

use Mpociot\VatCalculator\Facades\VatCalculator;

/**
 * eu format or uk format:<br>
 * @see https://github.com/driesvints/vat-calculator#get-eu-vat-number-details
 * @see https://github.com/driesvints/vat-calculator#uk-vat-numbers
 */
class VatDetailsFromVatID
{
    public static function lookup(string $vatID): object
    {
        $empty = new \stdClass([
            'countryCode' => '',
            'vatNumber' => '',
            'requestDate' => '',
            'valid' => false,
            'name' => '',
            'address' => '',
        ]);

        if(!$vatID) {
            return $empty;
        }

        try {
            return VatCalculator::getVATDetails($vatID) ?? $empty;
        } catch (\Exception) {
            return $empty;
        }
    }
}
