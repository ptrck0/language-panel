<?php

namespace Patrick\LanguagePanel\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LanguageLineImport implements ToModel, WithHeadingRow
{
    private array $locales;

    public function __construct()
    {
        $this->locales = config('language-panel.locales');
    }

    /**
     * @return null|Model
     */
    public function model(array $row)
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            if (array_key_exists($locale, $row)) {
                $translations[$locale] = $row[$locale];
            }
        }

        return LanguageLine::updateOrCreate(
            [
                'group' => $row['group'],
                'key' => $row['key'],
            ],
            ['text' => $translations],
        );
    }
}
