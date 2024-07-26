<?php

namespace Patrick\LanguagePanel;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Patrick\LanguagePanel\Resources\LanguageLineResource;

class LanguagePanel implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'language-panel';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            LanguageLineResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
