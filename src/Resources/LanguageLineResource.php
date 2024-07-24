<?php

namespace Patrick\LanguagePanel\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Patrick\LanguagePanel\Jobs\ImportFromLangFiles;
use Patrick\LanguagePanel\Resources\Helpers\FilterHelper;
use Patrick\LanguagePanel\Resources\Helpers\TableHelper;
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
                TextColumn::make('id')
                    ->label(__('language-panel::form.id'))
                    ->searchable(),
                TextColumn::make('group')
                    ->label(__('language-panel::form.group'))
                    ->searchable(),
                TextColumn::make('key')
                    ->label(__('language-panel::form.key'))
                    ->searchable(),
                ...TableHelper::makeIconColumns(fn (Model $record) => $record),
                TextColumn::make('updated_at')
                    ->date('d-m-Y')
                    ->label(__('language-panel::form.updated_at')),
            ])
            ->filters([
                ...FilterHelper::makeFilters(),
                /* TernaryFilter::make('has_english') */
                /*     ->label(__('language-panel::form.filter.has_english')) */
                /*     ->queries( */
                /*         true: fn (Builder $query) => $query->whereNotNull('text->en'), */
                /*         false: fn (Builder $query) => $query->orWhereNull('text->en') */
                /*             ->orWhereJsonDoesntContain('text', 'en'), */
                /*         blank: fn (Builder $query) => $query, */
                /*     ) */
                /*     ->trueLabel(__('language-panel::general.yes')) */
                /*     ->falseLabel(__('language-panel::general.no')), */
                /* TernaryFilter::make('has_dutch') */
                /*     ->label(__('language-panel::form.filter.has_dutch')) */
                /*     ->queries( */
                /*         true: fn (Builder $query) => $query->whereNotNull('text->nl'), */
                /*         false: fn (Builder $query) => $query->orWhereNull('text->nl') */
                /*             ->orWhereJsonDoesntContain('text', 'nl'), */
                /*         blank: fn (Builder $query) => $query, */
                /*     ) */
                /*     ->trueLabel(__('language-panel::general.yes')) */
                /*     ->falseLabel(__('language-panel::general.no')), */
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('update')
                    ->form([
                        Toggle::make('truncate'),
                        Toggle::make('overwrite'),
                    ])->requiresConfirmation()
            ])
            ->headerActions([
                Action::make(__('language-panel::form.action.import'))
                    ->form([
                        Toggle::make('truncate')
                            ->label(__('language-panel::form.action.form.truncate'))
                            ->visible(config('language-panel.import.allow_overwrite'))
                            ->onColor('danger')
                            ->offColor('success'),

                        Toggle::make('overwrite')
                            ->label(__('language-panel::form.action.form.overwrite'))
                            ->visible(config('language-panel.import.allow_truncate'))
                            ->onColor('danger')
                            ->offColor('success'),
                    ])
                    ->requiresConfirmation()
                    ->action(function (array $data) {
                        Notification::make('processing')
                            ->title(__('language-panel::form.notification.processing_lang_files'))
                            ->info()
                            ->send();
                        ImportFromLangFiles::dispatchSync(
                            $data['overwrite'],
                            $data['truncate']
                        );
                        Notification::make('finished')
                            ->title(__('language-panel::form.notification.done_processing_lang_files'))
                            ->success()
                            ->send();
                    })
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
}
