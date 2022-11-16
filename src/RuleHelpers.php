<?php

namespace TantHammar\LaravelRules;

use Mpociot\VatCalculator\Facades\VatCalculator;
use TantHammar\LaravelRules\Rules\OrgNummer;
use TantHammar\LaravelRules\Rules\PersonNummer;

class RuleHelpers
{
    /**
     * @param string $vatID
     * @return string
     * @deprecated use BusinessNameFromVatID::lookup(string $vatID)
     */
    public static function getBusinessNameFromVatID(string $vatID): string
    {
        try {
            return VatCalculator::getVATDetails($vatID)?->name ?? trans('laravel-rules::msg.vat-name-unknown');
        } catch (\Exception) {
            return trans('laravel-rules::msg.vat-name-unknown');
        }
    }

    /**
     * @param string $vatID
     * @return object
     * @deprecated use VatDetailsFromVatID::lookup(string $vatID)
     */
    public static function getVATDetailsFromVatID(string $vatID): object
    {
        $empty = new \stdClass([
            'countryCode' => '',
            'vatNumber' => '',
            'requestDate' => '',
            'valid' => false,
            'name' => '',
            'address' => '',
        ]);
        try {
            return VatCalculator::getVATDetails($vatID) ?? $empty;
        } catch (\Exception) {
            return $empty;
        }
    }

    /**
     * @param string|int $nr
     * @return string
     * @deprecated use BusinessTypeFromNr::make(string|int $nr)
     */
    public static function check_business_type(string|int $nr): string
    {
        if (filled($nr)) {
            if ((new PersonNummer())->passes(null, $nr)) {
                return 'individual';
            }
            if ((new OrgNummer())->passes(null, $nr)) {
                return 'business';
            }
        }
        return 'undefined';
    }
}
