<?php

namespace ZFort\Skeleton;

use Illuminate\Support\ServiceProvider;

class SkeletonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerResources();
        }

        $this->publishResources();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'skeleton');
    }

    /**
     * Register package resources.
     */
    protected function registerResources()
    {
        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'skeleton');

        // Translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'skeleton');

        // Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
    }

    protected function publishResources()
    {
        // Config
        $this->publishes([
            __DIR__.'/../config/skeleton.php' => config_path('skeleton.php'),
        ], 'config');

        // Views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/skeleton'),
        ], 'views');

        // Translations
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/social-auth'),
        ], 'lang');

        // Database
        if (! class_exists('CreateSocialProvidersTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../database/migrations/create_skeleton_table.php.stub' => $this->app->databasePath().'/migrations/'.$timestamp.'_create_skeleton_table.php',
            ], 'migrations');
        }
    }
}
