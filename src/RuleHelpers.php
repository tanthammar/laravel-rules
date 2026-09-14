<?php

namespace TantHammar\LaravelRules;

use TantHammar\LaravelRules\Services\BusinessNameFromVatID;
use TantHammar\LaravelRules\Services\VatDetailsFromVatID;

class RuleHelpers
{
    /**
     * @deprecated use BusinessNameFromVatID::lookup(string $vatID)
     */
    public static function getBusinessNameFromVatID(string $vatID): string
    {
       return BusinessNameFromVatID::lookup($vatID);
    }

    /**
     * @deprecated use VatDetailsFromVatID::lookup(string $vatID)
     */
    public static function getVATDetailsFromVatID(string $vatID): object
    {
        return VatDetailsFromVatID::lookup($vatID);
    }
}
