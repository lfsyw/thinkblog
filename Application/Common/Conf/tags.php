<?php

return [
    'app_init' => [
        'Snowair\Think\Behavior\HookAgent',
    ],

    'app_begin' => [
        'Behavior\CheckLangBehavior',
    ],

    'action_begin' => [
        'Home\Behavior\AuthBehavior',
    ],
];
