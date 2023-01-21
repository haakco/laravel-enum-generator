<?php

declare(strict_types=1);

return [
    'use-enum-format' => true,
    'default-leave-schema' => false,
    'default-uuid' => false,
    'default-prepend_class' => '',
    'default-prepend_name' => '',
    'enumPath' => app_path() . '/Models/Enums',
    'tables' => [
        //        'public.permissions' => [
        //            'uuid' => false,
        //            'leave-schema' => true,
        //            'prepend-class' => 'Rights',
        //            'prepend-name' => 'Rights',
        //        ],
    ],
];
