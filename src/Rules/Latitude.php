<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;

class Latitude implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }
        if (is_string($value)) {
            $value = (float) $value;
        }

        return $value >= -90 && $value <= 90;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('laravel-rules::messages.latitude');
    }
}
