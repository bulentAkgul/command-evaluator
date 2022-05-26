<?php

return [
    'default' => [
        'unknown' => "Unknown or unmatched {{ identifier }}: {{ issues }}",
        'reasonless' => "Unknown issue about {{ key }}."
    ],
    'app' => [
        'missing' => "The command has no app name." . PHP_EOL . "The files will be created in the shared folder." . PHP_EOL . " Proceed? (y / n / app name)",
        'unknown' => "There is no app named '{{ app }}.'" . PHP_EOL . " The files will be created in the shared folder of {{ package }}." . PHP_EOL . " Proceed? (y / n / app name)"
    ],
    'package' => [
        'duplicated' => "'{{ package }}' is already exist.",
        'missing' => [
            'file' => "The command has no package name." . PHP_EOL . " The files will be created in '/app'" . PHP_EOL . " Proceed? (y / n / package name)",
        ],
        'unknown' => [
            'file' => "A package named '{{ package }}' couldn't be found." . PHP_EOL . " The files will be created in the related namespace of the main repository." . PHP_EOL . " Proceed? (y / n / package name)",
            'relation' => "The given packages couldn't be found."
        ],
    ],
    'parent' => [
        'missing' => PHP_EOL . "To create a '{{ variation }}', a parent is required." . PHP_EOL . " Provide a {{ file }} name, which will be created unless it exists," . PHP_EOL . " or 'n' to terminate the command.",
        'conflicting' => PHP_EOL . "It contains more than one variation that requires a parent." . PHP_EOL . " Please create them ({{ variation }}) seperately."
    ],
    'type' => [
        'duplicated' => PHP_EOL . "Each file type in a single command must be unique." . PHP_EOL . " Please create them seperately.",
    ],
    'extra' => [
        'missing' => PHP_EOL . "You need to pass a file type to create a view without specifying the app." . PHP_EOL . " It's expected something like this -> view:component:blade."
    ],
    'root' => [
        'unknown' => "Unknown root: {{ root }}"
    ],
    'standalone' => [
        'unsuitable' => [
            'laravel' => "A standalone Laravel app can't have a package.",
            'package' => "A standalone package can have only one package," . PHP_EOL . " and it has already been created." . PHP_EOL . " That's way the command has been terminated.",
        ]
    ],
    'relation' => [
        'conflicting' => PHP_EOL . "There is no such thing as 'Polymorphic Has {{ count }} Through' relationship." . PHP_EOL . " 'One to {{ count }} Polymorphic' will be generated. Proceed? (y / n)",
        'excess' => PHP_EOL . "'Many To Many Polymorphic' relationship will create a pivot table in default name." . PHP_EOL . " Therefore, '{{ mediator }}' will be ignored. Proceed? (y / n)",
        'unknown' => "Unknown relation: {{ relation }}",
        'unusable' => PHP_EOL . "'Many To Many Polymorphic' relationship will use default column names." . PHP_EOL . " Therefore, the given column names will be ignored. Proceed? (y / n)",
    ],
    'model' => [
        'unknown' => "Unknown model: {{ model }}"
    ],
];
