<?php

namespace AdrienPoupa\MigrateRoutines;

use AdrienPoupa\MigrateRoutines\Console\MigrateFunctionsCommand;
use AdrienPoupa\MigrateRoutines\Console\MigrateProceduresCommand;
use AdrienPoupa\MigrateRoutines\Console\MigrateTriggersCommand;
use AdrienPoupa\MigrateRoutines\Console\MigrateViewsCommand;
use Illuminate\Support\ServiceProvider;

class MigrateRoutinesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateFunctionsCommand::class,
                MigrateProceduresCommand::class,
                MigrateTriggersCommand::class,
                MigrateViewsCommand::class,
            ]);
        }
    }
}
