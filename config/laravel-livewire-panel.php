<?php

declare(strict_types=1);

return [

    'default' => 'admin',

    'panels' => [

        'admin' => [
            'id'            => 'admin',
            'prefix'        => 'admin',
            'guard'         => 'web',
            'theme'         => 'bootstrap5',
            'customization' => 'style_table',
            'middleware'    => ['web', 'auth'],
            'gate'          => null,
            'registration_enabled' => true,
            'mode' => 'modules',

            'sidebar_menu' => [
                // ['label' => 'Dashboard', 'route' => 'panel.admin.dashboard.index', 'icon' => 'house'],
                // [
                //     'label' => 'Management',
                //     'icon'  => 'gear',
                //     'children' => [
                //         ['label' => 'Users', 'route' => 'panel.admin.users.index', 'icon' => 'users'],
                //     ],
                // ],
            ],

            'user_menu' => [
                // ['label' => 'Profile', 'route' => 'panel.admin.profile.index', 'icon' => 'user'],
                // ['label' => 'Settings', 'route' => 'panel.admin.settings.index', 'icon' => 'cog-6-tooth', 'permission' => 'settings.view'],
                // ['type'  => 'divider'],
                // ['label' => 'Stop Impersonating', 'route' => 'impersonate.leave', 'icon' => 'user-slash', 'visible' => fn () => session()->has('impersonated_by')],
            ],

            'navbar_components' => [
                'left'  => [],
                'right' => [],
            ],

            'components' => [
                'login'                        => null,
                'register'                     => null,
                'forgot-password'              => null,
                'reset-password'               => null,
                'forgot-password-notification' => null,
                'sidebar'                      => null,
                'navbar'                       => null,
            ],

            'locale' => [
                'enabled'      => false,
                'show_on_auth' => false,
                'available'    => [
                    'en' => 'English',
                    'es' => 'Español',
                    'fr' => 'Français',
                ],
            ],

            'cdn'           => [
                'chartjs' => [
                    'css'    => [],
                    'js'     => ['https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js'],
                    'routes' => [],
                ],
                'sweetalert2' => [
                    'css'    => ['https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'],
                    'js'     => ['https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'],
                    'routes' => [],
                ],
                'select2' => [
                    'css'    => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
                    'js'     => [
                        'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js',
                        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                    ],
                    'routes' => [],
                ],
                'flatpickr' => [
                    'css'    => ['https://cdn.jsdelivr.net/npm/flatpickr@4.6/dist/flatpickr.min.css'],
                    'js'     => ['https://cdn.jsdelivr.net/npm/flatpickr@4.6/dist/flatpickr.min.js'],
                    'routes' => [],
                ],
            ],
        ],

    ],

    'public_pages' => [

    ],

];
