<?php

namespace EntrieCloud\LaravelCockpit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use EntrieCloud\LaravelCockpit\Commands\LaravelCockpitCommand;

class LaravelCockpitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravelcockpit')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravelcockpit_table')
            ->hasCommand(LaravelCockpitCommand::class);
    }
}
