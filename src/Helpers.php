<?php

namespace TantHammar\LaravelRules;

class Helpers
{
    /**
     * Delete all characters except numbers in a string.
     * Intended use is to save clean phone numbers in db.
     */
    public static function clean_numbers(?string $number = ''): string|array|null
    {
        if ($number && $number !== '') {
            return preg_replace('/[^0-9]/', '', $number);
        }
        return $number;
    }
}
