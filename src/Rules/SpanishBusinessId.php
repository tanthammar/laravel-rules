<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates Spanish tax identification numbers:
 * - CIF: Company ID (Letter + 7 digits + control) Example: B82683907
 * - NIF/DNI: Spanish resident personal ID (8 digits + letter) Example: 12345678Z
 * - NIE: Foreigner or Non-Resident Entity ID (X/Y/Z + 7 digits + letter) Example: X1234567L
 *
 * Accepts optional ES prefix for VAT format (e.g., ESB82683907)
 */
class SpanishBusinessId implements Rule
{
    // NIF/NIE checksum: number % 23 maps to this letter sequence
    private const NIF_LETTERS = 'TRWAGMYFPDXBNJZSQVHLCKE';

    // Valid CIF organization type letters (excludes I, O, T to avoid confusion)
    private const CIF_LETTERS = 'ABCDEFGHJKLMNPQRSUVW';

    // CIF types requiring numeric control digit
    private const CIF_NUMERIC_CONTROL = 'ABEH';

    // CIF types requiring letter control digit
    private const CIF_LETTER_CONTROL = 'KPQSW';

    // CIF control letter mapping (0-9 → J,A,B,C,D,E,F,G,H,I)
    private const CIF_CONTROL_LETTERS = 'JABCDEFGHI';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return $this->isValidSpanishBusinessId($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('laravel-rules::messages.spanish-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Validate any Spanish tax ID (CIF, NIF/DNI, or NIE).
     */
    private function isValidSpanishBusinessId(string $id): bool
    {
        // Normalize: trim, remove ES prefix, remove separators, uppercase
        $id = trim($id);
        if (stripos($id, 'ES') === 0) {
            $id = substr($id, 2);
        }
        $id = strtoupper(str_replace([' ', '-', '.'], '', $id));

        if (strlen($id) !== 9) {
            return false;
        }

        $firstChar = $id[0];

        // NIE: X/Y/Z + 7 digits + letter (foreigners)
        if (in_array($firstChar, ['X', 'Y', 'Z'], true)) {
            if (! preg_match('/^[XYZ]\d{7}[A-Z]$/', $id)) {
                return false;
            }
            // Replace X=0, Y=1, Z=2, then use NIF algorithm
            $number = (int) (strtr($firstChar, 'XYZ', '012') . substr($id, 1, 7));
            return $id[8] === self::NIF_LETTERS[$number % 23];
        }

        // NIF/DNI: 8 digits + letter (Spanish residents)
        if (ctype_digit($firstChar)) {
            if (! preg_match('/^\d{8}[A-Z]$/', $id)) {
                return false;
            }
            $number = (int) substr($id, 0, 8);
            return $id[8] === self::NIF_LETTERS[$number % 23];
        }

        // CIF: Letter + 7 digits + control (companies)
        if (str_contains(self::CIF_LETTERS, $firstChar)) {
            if (! preg_match('/^[A-Z]\d{7}[A-Z0-9]$/', $id)) {
                return false;
            }
            return $this->isValidCifControl($id);
        }

        return false;
    }

    /**
     * Validate CIF control character (digit or letter depending on org type).
     */
    private function isValidCifControl(string $cif): bool
    {
        $orgType = $cif[0];
        $digits = substr($cif, 1, 7);
        $control = $cif[8];

        $expectedDigit = $this->calculateCifControlDigit($digits);
        $expectedLetter = self::CIF_CONTROL_LETTERS[$expectedDigit];

        // Some org types require numeric, some require letter, others accept both
        if (str_contains(self::CIF_NUMERIC_CONTROL, $orgType)) {
            return $control === (string) $expectedDigit;
        }
        if (str_contains(self::CIF_LETTER_CONTROL, $orgType)) {
            return $control === $expectedLetter;
        }
        return $control === (string) $expectedDigit || $control === $expectedLetter;
    }

    /**
     * Calculate CIF control digit using standard algorithm.
     */
    private function calculateCifControlDigit(string $digits): int
    {
        $sumOdd = 0;
        $sumEven = 0;

        for ($i = 0; $i < 7; $i++) {
            $digit = (int) $digits[$i];
            if ($i % 2 === 0) {
                // Odd positions: multiply by 2, sum digits if >= 10
                $doubled = $digit * 2;
                $sumOdd += $doubled >= 10 ? $doubled - 9 : $doubled;
            } else {
                // Even positions: add directly
                $sumEven += $digit;
            }
        }

        $lastDigit = ($sumOdd + $sumEven) % 10;
        return $lastDigit === 0 ? 0 : 10 - $lastDigit;
    }
}
