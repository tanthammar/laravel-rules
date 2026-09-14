<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates Danish identification numbers:
 * - CVR-nummer: 8-digit business number whose weighted sum over 2, 7, 6, 5, 4, 3, 2, 1
 *     is divisible by 11. Example: 24256790 (Novo Nordisk A/S).
 *     The DK prefix belongs to the VAT number, which is a different identifier, so it is
 *     rejected here. Denmark is in the EU, so its VAT number is verified through VIES.
 * - CPR-nummer: 10-digit personal number DDMMYYSSSS.
 *     Deliberately not checksummed. CPR stopped requiring modulus 11 on 1 October 2007
 *     because numbers ran out on busy birth dates, and instructs that "alle it-systemer bør
 *     indrettes således, at personnumre uden modulus 11 kan håndteres", so numbers failing
 *     it are valid and must not be rejected.
 *
 * @see https://www.cpr.dk/cpr-systemet/personnumre-uden-kontrolciffer-modulus-11-kontrol
 */
class DanishBusinessId implements Rule
{
    private const CVR_WEIGHTS = [2, 7, 6, 5, 4, 3, 2, 1];

    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return $this->isBusinessCode((string) $value) || $this->isPersonalCode((string) $value);
    }

    public function message(): string
    {
        return __('laravel-rules::messages.danish-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /** True if the value is a valid Danish CVR-nummer. */
    public function isBusinessCode(string $value): bool
    {
        $cvr = $this->normalize($value);

        if (strlen($cvr) !== 8 || ! ctype_digit($cvr)) {
            return false;
        }

        $sum = 0;
        foreach (self::CVR_WEIGHTS as $i => $weight) {
            $sum += (int) $cvr[$i] * $weight;
        }

        return $sum % 11 === 0;
    }

    /** True if the value is a valid Danish CPR-nummer. */
    public function isPersonalCode(string $value): bool
    {
        $cpr = $this->normalize($value);

        if (strlen($cpr) !== 10 || ! ctype_digit($cpr)) {
            return false;
        }

        // The century needs the sequence number to resolve, so the date is checked against
        // a leap year and only impossible day/month pairs are rejected.
        return checkdate((int) substr($cpr, 2, 2), (int) substr($cpr, 0, 2), 2000);
    }

    private function normalize(string $value): string
    {
        return str_replace([' ', '-', '.'], '', strtoupper(trim($value)));
    }
}
