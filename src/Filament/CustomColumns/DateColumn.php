<?php

namespace Patrick\LanguagePanel\Filament\CustomColumns;

use Filament\Tables\Columns\TextColumn as FilamentTextColumn;

class DateColumn
{
    public static function make(
        string $name,
        string $translationKey,
        bool $hidden = false,
    ): FilamentTextColumn {
        return FilamentTextColumn::make($name)
            ->date('d-m-Y')
            ->toggleable(isToggledHiddenByDefault: $hidden)
            ->label(__($translationKey))
        ;
    }
}
