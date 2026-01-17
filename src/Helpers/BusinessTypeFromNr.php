<?php

namespace TantHammar\LaravelRules\Helpers;

use App\Rules\FinnishBusinessId;
use TantHammar\LaravelRules\Rules\OrgNummer;
use TantHammar\LaravelRules\Rules\PersonNummer;

/** returns 'business', 'individual' or 'undefined' */
class BusinessTypeFromNr
{
    /** returns 'business', 'individual' or 'undefined' */
    public static function make(null | string | int $nr): string
    {
        if (filled($nr)) {
            if ((new PersonNummer)->passes(null, $nr)) {
                return 'individual';
            }
            if (
                (new OrgNummer)->passes(null, $nr) ||
                (new FinnishBusinessId)->passes(null, $nr)
            ) {
                return 'business';
            }
        }

        return 'undefined';
    }
}
