<?php

namespace TantHammar\LaravelRules\Enums;

enum BusinessNameLookupError: string
{
    case Unknown = 'unknown';
    case ServiceUnavailable = 'service_unavailable';
}