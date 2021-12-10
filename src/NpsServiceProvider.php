<?php

namespace Maize\Nps;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NpsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-nps')
            ->hasConfigFile()
            ->hasRoute('routes')
            ->hasMigration('create_nps_tables');
    }
}
