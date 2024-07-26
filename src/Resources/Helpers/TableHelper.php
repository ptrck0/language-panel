<?php

namespace Patrick\LanguagePanel\Resources\Helpers;

use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TableHelper
{
    public static function makeIconColumns(\Closure $record): array
    {
        $iconColumns = [];
        collect(config('language-panel.locales'))->each(function ($locale) use (&$iconColumns) {
            $iconColumns[] = IconColumn::make($locale)
                ->label(Str::of($locale)->upper())
                ->boolean()
                ->toggleable()
                ->state(fn(Model $record) => self::languageLineHasTranslation($locale, $record))
            ;
        });

        return $iconColumns;
    }

    private static function languageLineHasTranslation(
        string $locale,
        Model $record,
    ): bool {
        if (array_key_exists($locale, $record->text)) {
            return ! empty($record->text[$locale]);
        }

        return false;
    }
}
