<?php

namespace TantHammar\LaravelRules\Services;

use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;
use Mpociot\VatCalculator\VatCalculator;
use TantHammar\LaravelRules\Enums\BusinessNameLookupError;

class BusinessNameFromVatID
{
    public static function lookup(string $vatID): object
    {
        if (blank($vatID)) {
            return BusinessNameLookupError::Unknown;
        }

        try {
            // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
            $calculator = new VatCalculator(['forward_soap_faults' => true]);

            $object = $calculator->getVATDetails($vatID);

            return is_object($object) ? $object->name : BusinessNameLookupError::Unknown;

        } catch (VATCheckUnavailableException $e) {
            //if Country != GB, less verbose messages are returned.
            return BusinessNameLookupError::ServiceUnavailable;
        }
    }
}
