<?php

namespace Patrick\LanguagePanel\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Patrick\LanguagePanel\Resources\LanguageLineResource;

class ListLanguageLines extends ListRecords
{
    protected static string $resource = LanguageLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
