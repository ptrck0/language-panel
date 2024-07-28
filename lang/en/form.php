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
        'import' => 'Import from language-files',
        'form' => [
            'truncate' => 'Truncate language-table first',
            'overwrite' => 'Overwrite existing data with data from files',
        ],
        'upload' => 'Upload Spreadsheet',
        'download' => 'Download Spreadsheet',
    ],
    'action_group' => [
        'upload_download' => 'Upload/Download',
    ],
    'notification' => [
        'processing_lang_files' => 'One moment, the files are being imported...',
        'done_processing_lang_files' => 'The files are imported!',
        'processing_import_file' => 'One moment, the file is being processed',
        'done_processing_import_file' => 'The file is processed!',
    ],
];
