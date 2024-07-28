<?php

namespace Patrick\LanguagePanel\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Patrick\LanguagePanel\Resources\LanguageLineResource;

class CreateLanguageLine extends CreateRecord
{
    protected static string $resource = LanguageLineResource::class;
}
