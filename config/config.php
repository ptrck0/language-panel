<?php

return [
    // Your supported locales, only locales defined here are imported.
    'locales' => [
        'nl',
        'en',
    ],
    'excel' => [
        // Show the menu, allow importing, allow exporting.
        'allow_all' => true,
        'allow_import' => true,
        'allow_export' => true,
    ],
    'lang-import' => [
        // Allow truncating when importing, allow overwriting exsiting lines.
        'allow_truncate' => true,
        'allow_overwrite' => true,
        // Not yet supported.
        // 'add_vendor' => false,
    ],
    'resource' => [
        // Control the form actions that can be done.
        'form' => [
            'edit_form_group' => false,
            'edit_form_key' => false,
            'add_form_keyvalue' => false,
            'edit_form_keyvalue' => false,
            'delete_form_keyvalue' => false,
        ],
        // Allow creation or deletion of language line in resource.
        'allow_create' => false,
        'allow_delete' => false,
    ],
];
