<?php

namespace TantHammar\LaravelRules;

use Mpociot\VatCalculator\Facades\VatCalculator;
use TantHammar\LaravelRules\Rules\OrgNummer;
use TantHammar\LaravelRules\Rules\PersonNummer;

class RuleHelpers
{
    public static function getBusinessNameFromVatID(string $vatID): string
    {
        try {
            return VatCalculator::getVATDetails($vatID)?->name ?? trans('laravel-rules::msg.vat-name-unknown');
        } catch (\Exception) {
            return trans('laravel-rules::msg.vat-name-unknown');
        }
    }

    /** src https://github.com/driesvints/vat-calculator#get-eu-vat-number-details<br>
     * uk format: https://github.com/driesvints/vat-calculator#uk-vat-numbers
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

    /** returns 'business', 'individual' or 'undefined' */
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
