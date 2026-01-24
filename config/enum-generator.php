<?php

declare(strict_types=1);

return [
    'use-enum-format' => true,
    'default-leave-schema' => false,
    'default-uuid' => false,
    'id-field' => 'id',
    'name-field' => 'name',
    'default-prepend_class' => '',
    'default-prepend_name' => '',
    'enumPath' => app_path() . '/Models/Enums',
    'default-order-by' => ['name', 'id'],
    'tables' => [
        //        'public.permissions' => [
        //            'uuid' => false,
        //            'leave-schema' => true,
        //            'prepend-class' => 'Rights',
        //            'prepend-name' => 'Rights',
        //            'id-field' => 'id',
        //            'name-field' => 'name',
        //            // Filter rows by column values (only include matching rows)
        //            'where' => [
        //                'is_active' => true,
        //                'type' => 'public',
        //            ],
        //        ],
    ],
];
