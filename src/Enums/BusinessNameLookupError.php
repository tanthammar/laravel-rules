<?php

namespace TantHammar\LaravelRules\Enums;

enum BusinessNameLookupError: string
{
    case Unknown = 'unknown';
    case ServiceUnavailable = 'service_unavailable';
    case Invalid = 'invalid'; //Vat number is invalid, unable to get legal name

    public function label(): string
    {
        return match ($this) {
            self::Unknown => 'Unknown',
            self::ServiceUnavailable => 'Service Unavailable',
            self::Invalid => 'Invalid VAT ID',
        };
    }
}