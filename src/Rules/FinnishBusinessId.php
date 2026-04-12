<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates Finnish tax identification numbers:
 * - Y-tunnus: 8-digit business ID (7 digits + mod-11 check digit)
 * - HETU (henkilötunnus): 11-char personal identity code: DDMMYYCZZZQ
 *     C = century marker: + (1800s); -, Y, X, W, V, U (1900s); A, B, C, D, E, F (2000s)
 *     Q = check char from "0123456789ABCDEFHJKLMNPRSTUVWXY" indexed by DDMMYYZZZ mod 31
 *
 * Accepts optional FI prefix on Y-tunnus.
 */
class FinnishBusinessId implements Rule
{
    private const HETU_CHECK_CHARS = '0123456789ABCDEFHJKLMNPRSTUVWXY';

    private const HETU_CENTURIES = [
        '+' => 1800,
        '-' => 1900, 'Y' => 1900, 'X' => 1900, 'W' => 1900, 'V' => 1900, 'U' => 1900,
        'A' => 2000, 'B' => 2000, 'C' => 2000, 'D' => 2000, 'E' => 2000, 'F' => 2000,
    ];

    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return $this->isBusinessCode((string) $value) || $this->isPersonalCode((string) $value);
    }

    public function message(): string
    {
        return __('laravel-rules::messages.finnish-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /** True if the value is a valid Finnish Y-tunnus (business ID). */
    public function isBusinessCode(string $value): bool
    {
        $ytunnus = trim($value);

        if (stripos($ytunnus, 'FI') === 0) {
            $ytunnus = substr($ytunnus, 2);
        }

        $ytunnus = str_replace([' ', '-'], '', $ytunnus);

        if (strlen($ytunnus) !== 8 || ! ctype_digit($ytunnus)) {
            return false;
        }

        $weights = [7, 9, 10, 5, 8, 4, 2];
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += (int) $ytunnus[$i] * $weights[$i];
        }

        $remainder = $sum % 11;

        if ($remainder === 1) {
            return false;
        }

        $expected = $remainder === 0 ? 0 : 11 - $remainder;

        return $expected === (int) $ytunnus[7];
    }

    /** True if the value is a valid Finnish HETU (personal identity code). */
    public function isPersonalCode(string $value): bool
    {
        $hetu = strtoupper(trim($value));

        if (strlen($hetu) !== 11) {
            return false;
        }

        $date = substr($hetu, 0, 6);
        $sign = $hetu[6];
        $individual = substr($hetu, 7, 3);
        $check = $hetu[10];

        if (! ctype_digit($date) || ! ctype_digit($individual)) {
            return false;
        }

        if (! isset(self::HETU_CENTURIES[$sign])) {
            return false;
        }

        $day = (int) substr($date, 0, 2);
        $month = (int) substr($date, 2, 2);
        $year = self::HETU_CENTURIES[$sign] + (int) substr($date, 4, 2);

        if (! checkdate($month, $day, $year)) {
            return false;
        }

        // Individual number 000-001 is reserved and never issued.
        if ((int) $individual < 2) {
            return false;
        }

        $expected = self::HETU_CHECK_CHARS[((int) ($date . $individual)) % 31];

        return $check === $expected;
    }
}