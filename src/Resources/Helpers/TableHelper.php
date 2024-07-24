<?php

namespace Patrick\LanguagePanel\Resources\Helpers;

use Closure;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;

class TableHelper
{
    public static function makeIconColumns(Closure $record)
    {
        $iconColumns = [];
        collect(config('language-panel.locales'))->each(function ($name, $key) use (&$iconColumns) {
            $iconColumns[] = IconColumn::make($key)
                ->label($name)
                ->boolean()
                ->state(fn (Model $record) => self::languageLineHasTranslation($key, $record));
        });

        return $iconColumns;
    }

    private static function languageLineHasTranslation(
        string $locale,
        Model $record
    ): bool {

        if (array_key_exists($locale, $record->text)) {
            return ! empty($record->text[$locale]);
        }

        return false;
    }
}
