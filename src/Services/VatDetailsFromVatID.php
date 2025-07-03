<?php

namespace TantHammar\LaravelRules\Services;

/**
 * eu format or uk format:<br>
 *
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

        if (! $vatID) {
            return $empty;
        }

        // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
        $calculator = new \Mpociot\VatCalculator\VatCalculator(['forward_soap_faults' => true]);
        try {
            $object = $calculator->getVATDetails($vatID);
            return  is_object($object) ? $object : $empty;
        } catch (\Exception) {
            return $empty;
        }
    }
}
