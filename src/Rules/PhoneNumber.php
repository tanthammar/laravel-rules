<?php
namespace Tanthammar\LaravelRules\Rules;

use Brick\PhoneNumber\PhoneNumber as Validator;
use Brick\PhoneNumber\PhoneNumberParseException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class PhoneNumber implements Rule
{

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            if (!Str::of($value)->startsWith('+')) {
                $value = Str::of($value)->prepend('+');
            }
            return Validator::parse($value)->isValidNumber();
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
