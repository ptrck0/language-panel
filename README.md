# Language-panel

A [FilamentPHP](https://filamentphp.com/) plugin to load and manage translations
with.

This package does three things:

1.Loads translations from your `lang/` directory, and uses
[Spatie's translation loader](https://github.com/spatie/laravel-translation-loader)
to store and read your data.
2. Provides you with a filament admin panel plugin to manage the loaded translations.
3. Provides you with the option to download the translations in excel format,
edit them, and upload them again.

## Screenshots

![index](https://i.imgur.com/DYWGmuh.png)
![upload](https://i.imgur.com/cGkFfxF.png)
![import](https://i.imgur.com/Syemuma.png)

## Installation

- Install this package: `composer require patrick/language-panel`
- Add Spatie's translation loader to your `config/app.php` file:

```php
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()
        ->replace([
            Illuminate\Translation\TranslationServiceProvider::class => Spatie\TranslationLoader\TranslationServiceProvider::class,
        ])->toArray(),
```

- Publish the migrations of Spatie's package and run them:

```bash
php artisan vendor:publish --provider="Spatie\TranslationLoader\TranslationServiceProvider" --tag="migrations"
php artisan migrate
```

- Load your translations using artisan: `php artisan language-panel:import`.
- Publish the configuration assets to customize which functions of the panel
can be used:
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

## Great alternatives

- [kenepa/translation-manager](https://github.com/kenepa/translation-manager)
- [barryvdh/laravel-translation-manager](https://github.com/barryvdh/laravel-translation-manager)
