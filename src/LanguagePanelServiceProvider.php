<?php

namespace Patrick\LanguagePanel;

use Illuminate\Support\ServiceProvider;
use Patrick\LanguagePanel\Console\Commands\ImportTranslations;

class LanguagePanelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportTranslations::class,
            ]);
        }

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'language-panel');

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('language-panel.php'),
        ], 'language-panel-config');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/language-panel'),
        ], 'language-panel-lang');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'language-panel',
        );
    }
}
