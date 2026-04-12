<?php

namespace TantHammar\LaravelRules\Helpers;

use TantHammar\LaravelRules\Rules\EstonianBusinessId;
use TantHammar\LaravelRules\Rules\FinnishBusinessId;
use TantHammar\LaravelRules\Rules\SpanishBusinessId;
use TantHammar\LaravelRules\Rules\OrgNummer;
use TantHammar\LaravelRules\Rules\PersonNummer;

/** returns 'business', 'individual' or 'undefined' */
class BusinessTypeFromNr
{
    /** returns 'business', 'individual' or 'undefined' */
    public static function make(null | string | int $nr): string
    {
        if (filled($nr)) {
            $estonian = new EstonianBusinessId;
            $finnish = new FinnishBusinessId;

            if (
                $estonian->isPersonalCode((string) $nr) ||
                $finnish->isPersonalCode((string) $nr) ||
                (new PersonNummer)->passes(null, $nr)
            ) {
                return 'individual';
            }
            if (
                $estonian->isBusinessCode((string) $nr) ||
                $finnish->isBusinessCode((string) $nr) ||
                (new OrgNummer)->passes(null, $nr) ||
                (new SpanishBusinessId)->passes(null, $nr)
            ) {
                return 'business';
            }
        }

        return 'undefined';
    }
}
