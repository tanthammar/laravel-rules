<?php

namespace TantHammar\LaravelRules\Factories;

/**
 * Swedish mobile number formats, while waiting for PR
 *
 * @see https://github.com/FakerPHP/Faker/pull/491
 * @see https://www.pts.se/sv/bransch/telefoni/nummer-och-adressering/telefoninummerplanen/telefonnummers-struktur/
 */
class FakeMobileNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @var array Swedish mobile number formats
     */
    protected static $localMobileFormats = [
        '07########',
        '07## ## ## ##',
        '07## ### ###',
    ];

    protected static $internationalMobileFormats = [
        '+467########',
        '+46(0)7########',
        '+46 (0)7## ## ## ##',
        '+46 (0)7## ### ###',
    ];

    public static function make(bool $international = true): string
    {
        $format = static::randomElement($international ? static::$internationalMobileFormats : static::$localMobileFormats);

        $callback = function ($matches) {
            return $this->format($matches[1]);
        };

        $val = preg_replace_callback('/{{\s?(\w+|[\w\\\]+->\w+?)\s?}}/u', $callback, $format);

        return self::numerify($val);
    }
}
