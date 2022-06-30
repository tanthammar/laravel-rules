<?php

namespace TantHammar\LaravelRules;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRulesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-rules')
            ->hasTranslations();
    }

    public function bootingPackage(): void
    {
        Validator::extend('alpha_spaces', static function ($attribute, $value) {
            return is_string($value) && (preg_match('/^[\pL\s]+$/u', $value) > 0);
        });

        Validator::extend('alpha_dash_spaces', static function ($attribute, $value) {
            return is_string($value) && (preg_match('#^[\\\/\pL\s\pM\pN_-]+$#u', $value) > 0);
        });
    }

}
