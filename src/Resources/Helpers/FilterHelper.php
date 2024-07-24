<?php

namespace Patrick\LanguagePanel\Resources\Helpers;

use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FilterHelper
{
    public static function makeFilters(): array
    {
        $filters = [];

        collect(config('language-panel.locales'))->each(function ($name, $key) use (&$filters) {
            $filters[] = TernaryFilter::make("has_{$key}")
                ->label(__("language-panel::form.filter.has_{$key}"))
                ->queries(
                    true: fn (Builder $query) => $query->whereNotNull("text->{$key}"),
                    false: fn (Builder $query) => $query->orWhereNull("text->{$key}")
                        ->orWhereJsonDoesntContain('text', $key),
                    blank: fn (Builder $query) => $query,
                )
                ->trueLabel(__('language-panel::general.yes'))
                ->falseLabel(__('language-panel::general.no'));
        });

        return $filters;
    }
}
