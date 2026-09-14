<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates Norwegian identification numbers:
 * - Organisasjonsnummer: 9-digit business/organisation number closed by a mod-11 check
 *     digit over the weights 3, 2, 7, 6, 5, 4, 3, 2. Example: 923609016 (Equinor ASA).
 *     The NO prefix and MVA suffix belong to the VAT number, which is a different
 *     identifier, so they are rejected here. @see EuVatPrefixes::verifyWithoutVies()
 *     Brønnøysundregistrene issues from the 8xx and 9xx series today, but the specification
 *     does not require it, so the starting digit is not checked.
 * - Fødselsnummer: 11-digit personal identity number DDMMYYIIIKK, closed by two mod-11
 *     control digits. Example: 26059765131.
 *     D-nummer, issued to foreign residents, is accepted: it adds 40 to the day.
 *     H-nummer, internal to the health sector, is not: it adds 40 to the month.
 */
class NorwegianBusinessId implements Rule
{
    private const ORG_WEIGHTS = [3, 2, 7, 6, 5, 4, 3, 2];

    private const FIRST_CONTROL_WEIGHTS = [3, 7, 6, 1, 8, 9, 4, 5, 2];

    private const SECOND_CONTROL_WEIGHTS = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return $this->isBusinessCode((string) $value) || $this->isPersonalCode((string) $value);
    }

    public function message(): string
    {
        return __('laravel-rules::messages.norwegian-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /** True if the value is a valid Norwegian organisasjonsnummer. */
    public function isBusinessCode(string $value): bool
    {
        $id = $this->normalize($value);

        if (strlen($id) !== 9 || ! ctype_digit($id)) {
            return false;
        }

        $sum = 0;
        foreach (self::ORG_WEIGHTS as $i => $weight) {
            $sum += (int) $id[$i] * $weight;
        }

        $remainder = $sum % 11;

        // A remainder of 1 would need a check digit of 10, so no such number exists.
        if ($remainder === 1) {
            return false;
        }

        return ($remainder === 0 ? 0 : 11 - $remainder) === (int) $id[8];
    }

    /** True if the value is a valid Norwegian fødselsnummer or D-nummer. */
    public function isPersonalCode(string $value): bool
    {
        $fnr = $this->normalize($value);

        if (strlen($fnr) !== 11 || ! ctype_digit($fnr)) {
            return false;
        }

        $day = (int) substr($fnr, 0, 2);
        $month = (int) substr($fnr, 2, 2);

        if ($day > 40) {
            $day -= 40;
        }

        // The century needs the individnummer table to resolve, so the date is checked
        // against a leap year and only impossible day/month pairs are rejected.
        if (! checkdate($month, $day, 2000)) {
            return false;
        }

        $first = $this->controlDigit($fnr, self::FIRST_CONTROL_WEIGHTS);

        if ($first === null || $first !== (int) $fnr[9]) {
            return false;
        }

        // The second weight vector reads the first control digit back off the number,
        // which is safe now that it is known to be correct.
        return $this->controlDigit($fnr, self::SECOND_CONTROL_WEIGHTS) === (int) $fnr[10];
    }

    /** Mod-11 control digit over the leading digits, null when it would come out as 10. */
    private function controlDigit(string $fnr, array $weights): ?int
    {
        $sum = 0;
        foreach ($weights as $i => $weight) {
            $sum += (int) $fnr[$i] * $weight;
        }

        return match ($digit = 11 - ($sum % 11)) {
            10 => null,
            11 => 0,
            default => $digit,
        };
    }

    private function normalize(string $value): string
    {
        return str_replace([' ', '-', '.'], '', strtoupper(trim($value)));
    }
}
