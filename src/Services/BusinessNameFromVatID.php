<?php

namespace TantHammar\LaravelRules\Services;

use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;
use Mpociot\VatCalculator\VatCalculator;

class BusinessNameFromVatID
{
    public static function lookup(string $vatID): string
    {
        $unknown = trans('laravel-rules::messages.vat-name-unknown');

        if (blank($vatID)) {
            return $unknown;
        }

        try {
            // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
            $calculator = new VatCalculator(['forward_soap_faults' => true]);

            $object = $calculator->getVATDetails($vatID);

            return is_object($object) ? $object->name : $unknown;

        } catch (VATCheckUnavailableException $e) {
            //if Country != GB, less verbose messages are returned.
            return ($message = $e->getMessage()) === "MS_UNAVAILABLE"
                ? trans('laravel-rules::messages.vies-eu-unavailable')
                : $message;
        }
    }
}
