<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Command Evaluator
    |--------------------------------------------------------------------------
    |
    | You can disable the evaluators, but it's definitely not recommended.
    | Since the commands deal with the file system, you may break your app
    | when calling a command with some wrong settings. If you choose to
    | disable the evaluator, use the "file-history" package to roll back
    | the last state of your app. On the other hand, disabling the warnings
    | is perfectly safe after getting familiar with them.
    |
    */
    'evaluate_commands' => true,
    'disable_warnings' => false,
];