# Language-panel

A [FilamentPHP](https://filamentphp.com/) to load and manage translations with.

This package does two things:

1. It loads translations from your `lang/` directory, and uses
[Spatie's translation loader](https://github.com/spatie/laravel-translation-loader)
to store and read your data.
2. It provides you with a filament admin panel plugin to manage the loaded translations.

## Installation

- Install this package: `composer require patrick/language-panel`
- Add Spatie's translation loader to your `config/app.php` file:

```php
    'providers' => ServiceProvider::defaultProviders()
        ->replace([
            TranslationServiceProvider::class => Spatie\TranslationLoader\TranslationServiceProvider::class,
        ])->toArray(),
```

- Publish the migrations of Spatie's package and run them:

```bash
php artisan vendor:publish --provider="Spatie\TranslationLoader\TranslationServiceProvider" --tag="migrations"
php artisan migrate
```

- Load your translations using artisan: `php artisan language-panel:import`.
- Publish the configuration assets to further customize the options:
`php artisan vendor:publish --tag=language-panel-config`.
- Publish the translations if you would like to adjust them:
`php artisan vendor:publish --tag=language-panel-lang`.

## Usage

[Register](https://filamentphp.com/docs/3.x/panels/plugins#fluently-instantiating-the-plugin-class)
the following plugin in your admin panel: `Patrick\LanguagePanel\LanguagePanel:make()`

The panel will now be available at `/language-lines`, you can edit lines here that
you have imported.

## Todo

- [ ] Support vendor lines
- [ ] Implement import and export, to excel for example.
- [ ] Support other languages than English and Dutch.

## Great alternatives

- [kenepa/translation-manager](https://github.com/kenepa/translation-manager)
- [barryvdh/laravel-translation-manager](https://github.com/barryvdh/laravel-translation-manager)
