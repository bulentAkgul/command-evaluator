<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Command Evaluator
    |--------------------------------------------------------------------------
    |
    | You can disable the evaluators, but it's definitely not recommended.
    | Since the commands deal with the file system, you may breake your
    | app, in the case of calling a command with some wrong settings.
    | If you choose disabling the evaluator, use "file-history" package
    | to rollback prior state of your app. On the other hand, disabling
    | the warnings is perfectly safe after you get fimiliar with them.
    |
    */
    'evaluate_commands' => true,
    'disable_warnings' => false,
];