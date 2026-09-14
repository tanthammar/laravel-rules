<?php

namespace TantHammar\LaravelRules\Services;

use TantHammar\LaravelRules\Rules\NorwegianBusinessId;

/**
 * VAT prefixes covered by the VIES service.
 * Greece trades as EL for VAT purposes, not its ISO code GR.
 */
class EuVatPrefixes
{
    public const PREFIXES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL',
        'ES', 'FI', 'FR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
        'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    public static function of(string $vatID): string
    {
        return strtoupper(substr($vatID, 0, 2));
    }

    public static function normalize(string $vatID): string
    {
        return strtoupper(str_replace([' ', "\xC2\xA0", "\xA0", '-', '.', ','], '', trim($vatID)));
    }

    /**
     * Non-EU prefixes that VatCalculator can still regex check, but VIES cannot verify.
     */
    public const FORMAT_ONLY_PREFIXES = ['GB'];

    public static function contains(string $prefix): bool
    {
        return in_array($prefix, self::PREFIXES, true);
    }

    public static function hasFormatPattern(string $prefix): bool
    {
        return self::contains($prefix) || in_array($prefix, self::FORMAT_ONLY_PREFIXES, true);
    }

    /**
     * Prefixes that VIES cannot verify but that can still be checked in full, without an api call.
     * Norway is EEA rather than EU. Its VAT number is the organisasjonsnummer wrapped in an NO
     * prefix and an MVA suffix, the way a Swedish one is an organisationsnummer wrapped in an SE
     * prefix and an 01 suffix, so both wrappers are required and the mod-11 check digit verifies
     * the rest. Returns null when the prefix has no local check.
     */
    public static function verifyWithoutVies(string $vatID): ?bool
    {
        return match (self::of($vatID)) {
            'NO' => str_ends_with($vatID, 'MVA')
                && (new NorwegianBusinessId)->isBusinessCode(substr($vatID, 2, -3)),
            default => null,
        };
    }
}
