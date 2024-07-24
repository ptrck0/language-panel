<?php

return [
    //Your supported locales, the value is what is show in the UI.
    'locales' => [
        'nl' => 'NL',
        'en' => 'EN',
    ],
    'import' => [
        'allow_truncate' => true,
        'allow_overwrite' => true,
        //Not yet supported
        'add_vendor' => false,
    ],
    'resource' => [
        'form' => [
            'edit_form_group' => false,
            'edit_form_key' => false,
            'edit_form_lang_key' => false,
            'add_form_keyvalue' => false,
            'delete_form_keyvalue' => false,
        ],
    ],
];
