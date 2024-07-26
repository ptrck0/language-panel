<?php

namespace Patrick\LanguagePanel\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Database\Eloquent\Model;

class LanguageLineImport implements ToModel
{
    /**
     * @return null|Model
     */
    public function model(array $row)
    {
        return new LanguageLine([
        ]);
    }
}
