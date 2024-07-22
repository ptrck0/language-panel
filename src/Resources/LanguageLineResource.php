<?php

namespace Patrick\LanguagePanel\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Patrick\LanguagePanel\Resources\Pages\EditLanguageLine;
use Patrick\LanguagePanel\Resources\Pages\ListLanguageLines;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    protected static ?string $model = LanguageLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('group')
                                    ->label(__('language-panel::form.group'))
                                    ->disabled(! config('language-panel.resource.form.edit_form_group', false)),
                                TextInput::make('key')
                                    ->label(__('language-panel::form.key'))
                                    ->disabled(! config('language-panel.resource.form.edit_form_key', false)),
                            ])->columns(2),
                        Section::make()
                            ->schema([
                                KeyValue::make('text')
                                    ->label(__('language-panel::form.text'))
                                    ->keyLabel(__('language-panel::form.language'))
                                    ->valueLabel(__('language-panel::form.translation'))
                                    ->editableKeys(config('language-panel.resource.form.edit_form_lang_key', false))
                                    ->addable(config('language-panel.resource.form.add_form_keyvalue', false))
                                    ->deletable(config('language-panel.resource.form.delete_form_keyvalue', false)),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('id')
                        ->label(__('language-panel::form.id'))
                        ->searchable(),
                    TextColumn::make('group')
                        ->label(__('language-panel::form.group'))
                        ->searchable(),
                    TextColumn::make('key')
                        ->label(__('language-panel::form.key'))
                        ->searchable(),
                    IconColumn::make('en')
                        ->label('EN')
                        ->boolean()
                        ->state(fn (Model $record) => self::languageLineHasTranslation('en', $record)),
                    IconColumn::make('nl')
                        ->label('NL')
                        ->boolean()
                        ->state(fn (Model $record) => self::languageLineHasTranslation('nl', $record)),
                    TextColumn::make('updated_at')
                        ->date('d-m-Y')
                        ->label(__('language-panel::form.updated_at')),
                    TextColumn::make('updated_at')
                        ->date('d-m-Y')
                        ->label(__('language-panel::form.updated_at')),

                ]),
                Panel::make([
                    TextColumn::make('text')
                        ->label(__('language-panel::form.text'))
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->limitList(6)
                        ->searchable(),
                ])->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('has_english')
                    ->label(__('language-panel::form.filter.has_english'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('text->en'),
                        false: fn (Builder $query) => $query->orWhereNull('text->en')
                            ->orWhereJsonDoesntContain('text', 'en'),
                        blank: fn (Builder $query) => $query,
                    )
                    ->trueLabel(__('language-panel::general.yes'))
                    ->falseLabel(__('language-panel::general.no')),
                TernaryFilter::make('has_dutch')
                    ->label(__('language-panel::form.filter.has_dutch'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('text->nl'),
                        false: fn (Builder $query) => $query->orWhereNull('text->nl')
                            ->orWhereJsonDoesntContain('text', 'nl'),
                        blank: fn (Builder $query) => $query,
                    )
                    ->trueLabel(__('language-panel::general.yes'))
                    ->falseLabel(__('language-panel::general.no')),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguageLines::route('/'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }

    private static function languageLineHasTranslation(
        string $locale,
        Model $record
    ): bool {

        if (array_key_exists($locale, $record->text)) {
            return ! empty($record->text[$locale]);
        }

        return false;
    }
}
