<?php

namespace TantHammar\LaravelRules\Services;

use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;
use Mpociot\VatCalculator\VatCalculator;
use TantHammar\LaravelRules\Enums\BusinessNameLookupError;
use TantHammar\LaravelRules\Rules\VatNumberFormat;

class BusinessNameFromVatID
{
    public static function lookup(string $vatID): string | object
    {
        if (blank($vatID)) {
            return BusinessNameLookupError::Unknown;
        }

        try {

            $vatID = EuVatPrefixes::normalize($vatID);

            $prefix = EuVatPrefixes::of($vatID);

            // do simple validation before calling external api
            if (EuVatPrefixes::hasFormatPattern($prefix) && ! (new VatNumberFormat)->passes(null, $vatID)) {
                return BusinessNameLookupError::Invalid;
            }

            if (! EuVatPrefixes::contains($prefix)) {
                return ctype_alpha($prefix)
                    ? BusinessNameLookupError::Unsupported
                    : BusinessNameLookupError::Invalid;
            }

            // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
            $calculator = new VatCalculator(['forward_soap_faults' => true]);

            $object = $calculator->getVATDetails($vatID);

            if (is_object($object) && $object->valid === false) {
                return BusinessNameLookupError::Invalid;
            }

            if (is_object($object) && $object->valid === true) {
                if ($object->name === '---' || blank($object->name)) {
                    return BusinessNameLookupError::Unknown;
                }

                return $object->name;
            }

            return BusinessNameLookupError::Invalid;

        } catch (VATCheckUnavailableException $e) {
            // if Country != GB, less verbose messages are returned.
            return BusinessNameLookupError::ServiceUnavailable;
        }
    }
}
