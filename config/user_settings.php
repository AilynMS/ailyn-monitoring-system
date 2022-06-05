<?php

return [
    'validations' => [
        'password' => [
            'regex' => '/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            'min' => 6,
            'max' => '',
        ],
    ],
];
