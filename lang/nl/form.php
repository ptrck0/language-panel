<?php

return [
    'id' => 'ID',
    'group' => 'Groep',
    'key' => 'Naam',
    'text' => 'Vertalingen',
    'language' => 'Taal',
    'translation' => 'Vertaling',
    'updated_at' => 'Bijgewerkt op',
    'filter' => [
        'has_en' => 'Heeft Engels',
        'has_nl' => 'Heeft Nederlands',
    ],
    'action' => [
        'import' => 'Importeer uit vertaling-files',
        'form' => [
            'truncate' => 'Maak vertaling-tabel eerst leeg',
            'overwrite' => 'Overschrijf bestaande configuratie met data uit de bestanden',
        ],
    ],
    'notification' => [
        'processing_lang_files' => 'Een moment, de bestanden worden geimporteerd...',
        'done_processing_lang_files' => 'De files zijn geimporteerd!',
    ],
];
