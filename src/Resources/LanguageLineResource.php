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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
            ])
        ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::toggleAndSearchMacro('id', 'language-panel::form.id', true),
                TextColumn::toggleAndSearchMacro('group', 'language-panel::form.group'),
                TextColumn::toggleAndSearchMacro('key', 'language-panel::form.key'),
                ...TableHelper::makeIconColumns(fn(Model $record) => $record),
                TextColumn::make('text')
                    ->state(function (LanguageLine $record) {
                        $state = [];
                        foreach ($record->text as $text) {
                            if (str($text)->length()) {
                                $state[] = str($text)->words(3, '...');
                            }
                        }

                        return Arr::join($state, ', ');
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(function (LanguageLine $record) {
                        $state = [];
                        foreach ($record->text as $text) {
                            if (str($text)->length()) {
                                $state[] = $text;
                            }
                        }

                        return Arr::join($state, ', ');
                    }),
                TextColumn::dateMacro('updated_at', 'language-panel::form.updated_at', true),
            ])
            ->filters([
                ...FilterHelper::makeFilters(),
            ], layout: FiltersLayout::Dropdown)
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('update')
                    ->form([
                        Toggle::make('truncate'),
                        Toggle::make('overwrite'),
                    ])->requiresConfirmation(),
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
                            ->send()
                        ;
                        ImportFromLangFiles::dispatchSync(
                            $data['overwrite'],
                            $data['truncate'],
                        );
                        Notification::make('finished')
                            ->title(__('language-panel::form.notification.done_processing_lang_files'))
                            ->success()
                            ->send()
                        ;
                    }),
            ])
            ->bulkActions([])
        ;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguageLines::route('/'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }
}
