<?php

namespace TantHammar\LaravelRules;

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

}
