<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class SpanishBusinessId implements Rule
{
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
     * Validate Spanish NIF/CIF (Business ID).
     *
     * Format: Letter + 7 digits + control character (digit or letter)
     * Example: B12345678, A58818501
     */
    private function isValidSpanishBusinessId(string $cif): bool
    {
        // Remove whitespace
        $cif = trim($cif);

        // Remove ES prefix if present (case insensitive) - used for VAT numbers
        if (stripos($cif, 'ES') === 0) {
            $cif = substr($cif, 2);
        }

        // Remove hyphens and spaces
        $cif = str_replace([' ', '-'], '', $cif);

        // Convert to uppercase
        $cif = strtoupper($cif);

        // Must be exactly 9 characters
        if (strlen($cif) !== 9) {
            return false;
        }

        // Valid organization type letters for Spanish CIF
        $validLetters = 'ABCDEFGHJKLMNPQRSUVW';

        // First character must be a valid organization type letter
        $firstLetter = $cif[0];
        if (strpos($validLetters, $firstLetter) === false) {
            return false;
        }

        // Characters 2-8 must be digits (7 digits total)
        $digits = substr($cif, 1, 7);
        if (!ctype_digit($digits)) {
            return false;
        }

        // Calculate control digit
        $controlDigit = $this->calculateControlDigit($digits);

        // Last character is the control character
        $controlChar = $cif[8];

        // Letters that require numeric control digit
        $numericControlLetters = 'ABEH';

        // Letters that require letter control digit
        $letterControlLetters = 'PQSW';

        // Control letter mapping (0-9 maps to J, A, B, C, D, E, F, G, H, I)
        $controlLetters = 'JABCDEFGHI';

        if (strpos($numericControlLetters, $firstLetter) !== false) {
            // Must be a digit
            return ctype_digit($controlChar) && (int) $controlChar === $controlDigit;
        }

        if (strpos($letterControlLetters, $firstLetter) !== false) {
            // Must be a letter
            return $controlChar === $controlLetters[$controlDigit];
        }

        // For other letters, can be either digit or letter
        if (ctype_digit($controlChar)) {
            return (int) $controlChar === $controlDigit;
        }

        return $controlChar === $controlLetters[$controlDigit];
    }

    /**
     * Calculate the control digit for Spanish CIF.
     */
    private function calculateControlDigit(string $digits): int
    {
        $sumOdd = 0;
        $sumEven = 0;

        for ($i = 0; $i < 7; $i++) {
            $digit = (int) $digits[$i];

            if ($i % 2 === 0) {
                // Odd positions (0, 2, 4, 6): multiply by 2, subtract 9 if >= 10
                $doubled = $digit * 2;
                $sumOdd += $doubled >= 10 ? $doubled - 9 : $doubled;
            } else {
                // Even positions (1, 3, 5): add directly
                $sumEven += $digit;
            }
        }

        $total = $sumOdd + $sumEven;
        $lastDigit = $total % 10;

        return $lastDigit === 0 ? 0 : 10 - $lastDigit;
    }
}
