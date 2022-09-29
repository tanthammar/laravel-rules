<?php

namespace TantHammar\LaravelRules\Factories;

use byrokrat\banking\PlusgiroFactory;
use byrokrat\banking\Validator\Modulo10;

class FakePlusgiro
{
    public static function make(): string
    {
        // Generate general account number and calculate checksum
        $numString = fake()->regexify('[0-9]{7}');
        $numString .= Modulo10::calculateCheckDigit($numString);

        // Get formatted number
        return (new PlusgiroFactory)->createAccount($numString)->getNumber();
    }
}
