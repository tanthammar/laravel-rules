<?php

namespace TantHammar\LaravelRules;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRulesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        //More info: https://github.com/spatie/laravel-package-tools
        $package->name('laravel-rules');
    }

    public function bootingPackage(): void
    {
        Validator::extend('alpha_space', static function ($attribute, $value) {
            return is_string($value) && preg_match('/^[\pL\s]+$/u', $value);
        });

        Validator::extend('alpha_num_space', static function ($attribute, $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                return false;
            }

            return preg_match('/^[\pL\pM\pN\s]+$/u', $value) > 0;
        });

        Validator::extend('alpha_dash_space', static function ($attribute, $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                return false;
            }

            return preg_match('#^[\\\/\pL\s\pM\pN.,_-]+$#u', $value) > 0;
        });

        $this->bootTranslations();
    }

    //override spatie package shortname() that strips out 'laravel'
    public function bootTranslations()
    {
        $name = $this->package->name;
        $langPath = resource_path('lang/'.'vendor/'.$name);

        $this->publishes([
            $this->package->basePath('/../resources/lang') => $langPath,
        ], "$name-translations");

        $this->loadTranslationsFrom($this->package->basePath('/../resources/lang/'), $name);

        $this->loadJsonTranslationsFrom($this->package->basePath('/../resources/lang/'));
        $this->loadJsonTranslationsFrom($langPath);
    }
}
