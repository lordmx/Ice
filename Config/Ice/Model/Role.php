<?php
return [
    'mapping' => [
        'role_pk' => 'role_pk',
        'role_name' => 'role_name',
    ],
    'Ice\\Core\\Validator' => [
        'role_pk' => [
            0 => 'Ice:Not_Null',
        ],
        'role_name' => [],
    ],
    'Ice\\Core\\Form' => [
        'role_pk' => 'Number',
        'role_name' => 'Text',
    ],
];