<?php

namespace TantHammar\LaravelRules\Rules;

use Brick\PhoneNumber\PhoneNumber as Validator;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Illuminate\Contracts\Validation\Rule;

/**
 * The $value must start with a country code, like 46 for Sweden. "+" is prepended, if missing.
 */
class MobileNumber implements Rule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        try {
            if (! str_starts_with($value, '+')) {
                $value = '+' . $value;
            }
            $number = Validator::parse((string) $value);

            return $number->isValidNumber() && $number->getNumberType() === PhoneNumberType::MOBILE;
        } catch (PhoneNumberParseException $e) {
            return false;
        }
    }

    public function message(): string
    {
        return __('laravel-rules::messages.mobile-phone');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
