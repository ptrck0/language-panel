<?php

namespace Patrick\LanguagePanel\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Support\Collection;

class LanguageLineExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return LanguageLine::all();
    }
}
