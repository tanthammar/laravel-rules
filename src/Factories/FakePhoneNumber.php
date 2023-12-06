<?php

namespace TantHammar\LaravelRules\Factories;

/**
 * Swedish phone number formats, both landline and mobile
 *
 * @see https://fakerphp.github.io/locales/sv_SE/#fakerprovidersv_sephonenumber
 */
class FakePhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $localFormats = [
        '08-### ### ##',
        '0%#-### ## ##',
        '0%########',
        '08-### ## ##',
        '0%#-## ## ##',
        '0%##-### ##',
        '0%#######',
        '08-## ## ##',
        '0%#-### ###',
        '0%#######',
    ];

    protected static $internationalFormats = [
        '+46 (0)%## ### ###',
        '+46(0)%########',
        '+46 %## ### ###',
        '+46%########',

        '+46 (0)8 ### ## ##',
        '+46 (0)%# ## ## ##',
        '+46 (0)%## ### ##',
        '+46 (0)%#######',
        '+46(0)%#######',
        '+46%#######',

        '+46 (0)%######',
        '+46(0)%######',
        '+46%######',
    ];

    public static function make(bool $international = true): string
    {
        $format = static::randomElement($international ? static::$internationalFormats : static::$localFormats);

        $callback = function ($matches) {
            return $this->format($matches[1]);
        };

        $val = preg_replace_callback('/{{\s?(\w+|[\w\\\]+->\w+?)\s?}}/u', $callback, $format);

        return self::numerify($val);
    }

    /* Publishable GDPR Safe Swedish landline phone number series */
    public static function gdprSafe(): string
    {
        return fake()->randomElement([
            '031-3900' . fake()->numberBetween(600, 699),
            '040-6280' . fake()->numberBetween(400, 499),
            '08-46500' . fake()->numberBetween(400, 499),
            '0980-319' . fake()->numberBetween(200, 299),
        ]);
    }
}
