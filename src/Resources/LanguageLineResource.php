<?php

namespace Patrick\LanguagePanel\Resources;

use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Patrick\LanguagePanel\Exports\LanguageLineExport;
use Patrick\LanguagePanel\Imports\LanguageLineImport;
use Patrick\LanguagePanel\Jobs\ImportFromLangFiles;
use Patrick\LanguagePanel\Resources\Helpers\FilterHelper;
use Patrick\LanguagePanel\Resources\Helpers\TableHelper;
use Patrick\LanguagePanel\Resources\Pages\CreateLanguageLine;
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
                                    ->editableKeys(config('language-panel.resource.form.edit_form_keyvalue', false))
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
                            ->visible(config('language-panel.lang-import.allow_overwrite', false))
                            ->onColor('danger')
                            ->offColor('success'),

                        Toggle::make('overwrite')
                            ->label(__('language-panel::form.action.form.overwrite'))
                            ->visible(config('language-panel.lang-import.allow_truncate', false))
                            ->onColor('danger')
                            ->offColor('success'),
                    ])
                    ->requiresConfirmation()
                    ->action(function (array $data) {
                        Notification::make('processing_lang')
                            ->title(__('language-panel::form.notification.processing_lang_files'))
                            ->info()
                            ->send()
                        ;
                        ImportFromLangFiles::dispatchSync(
                            $data['overwrite'],
                            $data['truncate'],
                        );
                        Notification::make('finished_lang')
                            ->title(__('language-panel::form.notification.done_processing_lang_files'))
                            ->success()
                            ->send()
                        ;
                    })
                    ->icon('heroicon-s-arrow-up-circle'),
                ActionGroup::make([
                    Action::make('download')
                        ->action(fn() => Excel::download(new LanguageLineExport(), 'export.xlsx', ExcelFormat::XLSX))
                        ->visible(config('language-panel.excel.allow_export')),
                    Action::make('upload')
                        ->form([
                            FileUpload::make('importfile')
                                ->acceptedFileTypes([
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel',
                                ])
                                ->previewable(false)
                                ->storeFiles(false),

                            Toggle::make('truncate')
                                ->label(__('language-panel::form.action.form.truncate'))
                                ->visible(config('language-panel.lang-import.allow_truncate', false))
                                ->onColor('danger')
                                ->offColor('success'),
                        ])
                        ->requiresConfirmation()
                        ->action(function (array $data) {
                            Notification::make('processing_import')
                                ->title(__('language-panel::form.notification.processing_import_file'))
                                ->info()
                                ->send()
                            ;

                            if (Arr::exists($data, 'truncate') && $data['truncate']) {
                                LanguageLine::query()->truncate();
                            }

                            Excel::import(new LanguageLineImport(), $data['importfile']);

                            Notification::make('finished_import')
                                ->title(__('language-panel::form.notification.done_processing_import_file'))
                                ->success()
                                ->send()
                            ;
                        })
                        ->visible(config('language-panel.excel.allow_import', false)),
                ])->button()
                    ->label('Import/Export')
                    ->icon('heroicon-m-table-cells')
                    ->visible(config('language-panel.excel.allow_all', false)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(config('language-panel.resource.allow_delete', false)),
            ])
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
            'create' => CreateLanguageLine::route('/create'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }
}
