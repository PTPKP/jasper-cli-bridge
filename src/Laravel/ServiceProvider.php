<?php

namespace PTPKP\JasperCliBridge\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use PTPKP\JasperCliBridge\Configuration;
use PTPKP\JasperCliBridge\JasperReportService;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/jasper-cli-bridge.php',
            'jasper-cli-bridge'
        );

        // Register the Configuration singleton
        $this->app->singleton(Configuration::class, function ($app) {
            $config = $app['config']->get('jasper-cli-bridge', []);
            return new Configuration($config);
        });

        // Register the JasperReportService
        $this->app->singleton(JasperReportService::class, function ($app) {
            $config = $app->make(Configuration::class);
            return new JasperReportService($config);
        });

        // Register aliases for easier access
        $this->app->alias(JasperReportService::class, 'jasper');
        $this->app->alias(JasperReportService::class, 'jasper.reports');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/jasper-cli-bridge.php' => config_path('jasper-cli-bridge.php'),
            ], 'jasper-config');

            // Register commands
            $this->commands([
                JasperBuildCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            Configuration::class,
            JasperReportService::class,
            'jasper',
            'jasper.reports',
        ];
    }
}
