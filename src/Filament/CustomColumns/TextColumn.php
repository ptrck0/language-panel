<?php

namespace Patrick\LanguagePanel\Filament\CustomColumns;

use Filament\Tables\Columns\TextColumn as FilamentTextColumn;

class TextColumn
{
    public static function make(
        string $name,
        string $translationKey,
        bool $hidden = false,
    ) {
        return FilamentTextColumn::make($name)
            ->label(__($translationKey))
            ->searchable()
            ->toggleable(isToggledHiddenByDefault: $hidden)
        ;
    }
}
