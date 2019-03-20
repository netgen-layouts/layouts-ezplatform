<?php

declare(strict_types=1);

$Module = [
    'name' => 'nglayouts',
    'variable_params' => false,
    'ui_component_match' => 'module',
];

$ViewList = [];

if (!interface_exists('Netgen\Layouts\Enterprise\API\Service\RoleService')) {
    $FunctionList = [
        'admin' => [],
        'editor' => [],
        'api' => [],
    ];
}
