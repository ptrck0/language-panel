<?php

return [
    'id' => 'ID',
    'group' => 'Group',
    'key' => 'Key',
    'text' => 'Translations',
    'language' => 'Language',
    'translation' => 'Translation',
    'updated_at' => 'Updated at',
    'filter' => [
        'has_en' => 'Has English',
        'has_nl' => 'Has Dutch',
    ],
    'action' => [
        'import' => 'Import from project',
        'form' => [
            'truncate' => 'Truncate language-table first',
            'overwrite' => 'Overwrite existing data with data from files',
        ]
    ],
    'notification' => [
        'processing_lang_files' => 'One moment, the files are being imported...',
        'done_processing_lang_files' => 'The files are imported!',
    ]
];
