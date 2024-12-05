<?php

namespace EntrieCloud\LaravelCockpit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCockpitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cockpit')
            ->hasConfigFile('cockpit')
            ->hasRoute('web');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('laravel-cockpit', fn () => new LaravelCockpitManager);
    }
}
