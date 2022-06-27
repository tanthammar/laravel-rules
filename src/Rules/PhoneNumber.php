<?php
namespace Tanthammar\LaravelRules\Rules;

use Brick\PhoneNumber\PhoneNumber as Validator;
use Brick\PhoneNumber\PhoneNumberParseException;
use Illuminate\Contracts\Validation\Rule;

/**
 * The $value must start with a country code, like 46 for Sweden. "+" is prepended, if missing.
 */
class PhoneNumber implements Rule
{

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if(blank($value)) {
            return false;
        }
        try {
            if (!str_starts_with($value, '+')) {
                $value = "+".$value;
            }
            return Validator::parse((string)$value)->isValidNumber();
        } catch (PhoneNumberParseException $e) {
            return false;
        }
    }

    public function message(): string
    {
        if (app()->getLocale() === 'sv') {
            return 'Numret måste vara ett giltigt telefonnummer';
        }

        return 'The number must be a valid phone number';
    }
}
