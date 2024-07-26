<?php

namespace Patrick\LanguagePanel\Resources\Helpers;

use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FilterHelper
{
    public static function makeFilters(): array
    {
        $filters = [];

        collect(config('language-panel.locales'))->each(function ($locale) use (&$filters) {
            $filters[] = TernaryFilter::make("has_{$locale}")
                ->label(
                    __("language-panel::form.filter.has_{$locale}") ??
                        Str::of($locale)->upper(),
                )
                ->queries(
                    true: fn(Builder $query) => $query->whereNotNull("text->{$locale}"),
                    false: fn(Builder $query) => $query->whereNull("text->{$locale}")
                        ->orWhereJsonDoesntContain('text', $locale),
                    blank: fn(Builder $query) => $query,
                )
                ->trueLabel(__('language-panel::general.yes'))
                ->falseLabel(__('language-panel::general.no'))
            ;
        });

        return $filters;
    }
}
