<?php

namespace Patrick\LanguagePanel;

use Filament\Tables\Columns\TextColumn;
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

        TextColumn::macro('toggleAndSearchMacro', function ($name, $translationKey, $hidden = false) {
            return static::make($name)
                ->label(__($translationKey))
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: $hidden)
            ;
        });

        TextColumn::macro('dateMacro', function ($name, $translationKey, $hidden = false) {
            return static::make($name)
                ->date('d-m-Y')
                ->toggleable(isToggledHiddenByDefault: $hidden)
                ->label(__($translationKey))
            ;
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'language-panel',
        );
    }
}
