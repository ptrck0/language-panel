<?php

namespace Patrick\LanguagePanel\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineExport implements FromCollection, WithHeadings, WithMapping
{
    private array $locales;

    public function __construct()
    {
        $this->locales = config('language-panel.locales');
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return LanguageLine::all();
    }

    public function headings(): array
    {
        $headers = [
            'group',
            'key',
        ];

        return array_merge($headers, $this->locales);
    }

    public function map($languageLine): array
    {
        $columns = [
            'group' => $languageLine->group,
            'key' => $languageLine->key,
        ];

        foreach ($this->locales as $locale) {
            if (array_key_exists($locale, $languageLine->text)) {
                $columns[$locale] = $languageLine->text[$locale];
            } else {
                $columns[$locale] = null;
            }
        }

        return $columns;
    }
}
