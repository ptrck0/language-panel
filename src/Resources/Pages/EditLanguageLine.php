<?php

namespace Patrick\LanguagePanel\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Patrick\LanguagePanel\Resources\LanguageLineResource;

class EditLanguageLine extends EditRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
