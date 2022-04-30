<?php

return [
    'default' => [
        'unknown' => "Unknown or unmatched {{ identifier }}: {{ issues }}",
        'reasonless' => "The command has been terminated because of an unknown issue about {{ key }}."
    ],
    'app' => [
        'missing' => "The command has no app name." . PHP_EOL . "The files will be created in the shared folder." . PHP_EOL . " Proceed? (y / n / app name)",
        'unknown' => "There is no app named '{{ app }}.'" . PHP_EOL . " The files will be created in the shared folder of {{ package }}." . PHP_EOL . " Proceed? (y / n / app name)"
    ],
    'package' => [
        'duplicated' => "The command has been terminated because '{{ package }}' is already exist.",
        'missing' => [
            'file' => "The command has no package name." . PHP_EOL . " The files will be created in '/app'" . PHP_EOL . " Proceed? (y / n / package name)",
        ],
        'unknown' => [
            'file' => "A package named '{{ package }}' couldn't be found." . PHP_EOL . " The files will be created in the related namespace of the main repository." . PHP_EOL . " Proceed? (y / n / package name)",
            'relation' => "The command has been terminated because one of the given packages couldn't be found."
        ],
    ],
    'parent' => [
        'missing' => PHP_EOL . "To create a '{{ variation }}', a parent is required." . PHP_EOL . " Provide a {{ file }} name, which will be created unless it exists," . PHP_EOL . " or 'n' to terminate the command.",
        'conflicting' => PHP_EOL . "It contains more than one variation that requires a parent." . PHP_EOL . " Please create them ({{ variation }}) seperately."
    ],
    'type' => [
        'duplicated' => PHP_EOL . "Each file type in a single command must be unique." . PHP_EOL . " Please create them seperately.",
        'conflicting' => PHP_EOL . "There is no such thing as 'Polymorphic Has {{ count }} Through' relationship." . PHP_EOL . " 'One to {{ count }} Polymorphic' will be generated. Proceed? (y / n)",
    ],
    'extra' => [
        'missing' => PHP_EOL . "You need to pass a file type to create a view without specifying the app." . PHP_EOL . " It's expected something like this -> view:component:blade."
    ],
    'root' => [
        'unknown' => "Unknown root: {{ root }}"
    ],
    'standalone' => [
        'unsuitable' => [
            'laravel' => 'A standalone Laravel app can\'t have a package.',
            'package' => "A standalone package can have only one package," . PHP_EOL . " and it has already been created." . PHP_EOL . " That's way the command has been terminated.",
        ]
    ],
    'relation' => [
        'unknown' => "The command has been terminated because of an unknown relation: {{ relation }}"
    ],
    'model' => [
        'unknown' => "The command has been terminated because of an unknown model: {{ model }}"
    ],
    'command' => [
        'nothing' => 'There is no log file to execute this command.',
        'nomore' => 'There is no more log file to execute this command again.',
        'pairless' => 'The required log file is missing.'
    ],
];
