<?php

return [

    'connection' => null,

    'separator' => '.',

    'models' => [
        'role' => Endone777\Roles\Models\Role::class,
        'permission' => Endone777\Roles\Models\Permission::class,
    ],

    'pretend' => [

        'enabled' => false,

        'options' => [
            'hasRole' => true,
            'hasPermission' => true,
            'allowed' => true,
        ],

    ],

];
