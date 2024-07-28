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
        // 'add_vendor' => true,
    ],
    'resource' => [
        // Control the form actions that can be done.
        'form' => [
            'edit_form_group' => true,
            'edit_form_key' => true,
            'add_form_keyvalue' => true,
            'edit_form_keyvalue' => true,
            'delete_form_keyvalue' => true,
        ],
        // Allow creation or deletion of language line in resource.
        'allow_create' => true,
        'allow_delete' => true,
    ],
];
