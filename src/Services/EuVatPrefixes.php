<?php

namespace TantHammar\LaravelRules\Services;

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

    public static function normalize(?string $vatID): ?string
    {
        if (blank($vatID)) {
            return null;
        }

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
}
