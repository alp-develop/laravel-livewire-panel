<?php

declare(strict_types=1);

return [

    'id' => 'style_table',

    'sidebar' => [
        'initial_state'             => 'expanded',
        'collapsible'               => true,
        'icons_only_when_collapsed' => true,
        'persist_state'             => true,
        'overlay_on_mobile'         => true,
        'logo_height'               => '40px',
        'logo_width'                => 'auto',
        'logo_class'                => '',
        'logo'                      => null,
        'header_text'               => 'Panel Admin',
        'show_user_menu'            => false,
        'show_avatar'               => true,
    ],

    'navbar' => [
        'sticky'                         => true,
        'show_search'                    => true,
        'show_notifications'             => true,
        'show_breadcrumbs'               => true,
        'show_user_menu'                 => true,
        'show_avatar'                    => true,
        'show_page_title'                => true,
        'notification_polling'           => true,
        'notification_polling_interval'  => 30,
    ],

    'theming' => [
        'font_family'   => 'Inter, sans-serif',
        'font_size'     => '14px',
        'border_radius' => '8px',

        'primary'   => '#4f46e5',
        'secondary' => '#6c757d',
        'success'   => '#198754',
        'danger'    => '#dc3545',
        'warning'   => '#ffc107',
        'info'      => '#0dcaf0',

        'sidebar' => [
            'width'           => '260px',
            'collapsed_width' => '64px',
            'item_font_size'  => '0.9rem',
            'item_font_weight' => '600',
            'light' => [
                'background'  => '#1e293b',
                'text'        => '#cbd5e1',
                'muted'       => '#64748b',
                'active_bg'   => null,
                'active_text' => '#ffffff',
            ],
            'dark' => [
                'background'  => '#0f172a',
                'text'        => '#94a3b8',
                'muted'       => '#475569',
                'active_bg'   => null,
                'active_text' => '#ffffff',
            ],
        ],

        'navbar' => [
            'height' => '60px',
            'light' => [
                'background'       => '#ffffff',
                'text'             => '#1e293b',
                'border'           => '#e2e8f0',
                'icons_color'      => '#64748b',
                'icons_hover_color' => '#334155',
            ],
            'dark' => [
                'background'       => '#1e293b',
                'text'             => '#e2e8f0',
                'border'           => '#334155',
                'icons_color'      => '#94a3b8',
                'icons_hover_color' => '#e2e8f0',
            ],
        ],

        'panel' => [
            'light' => [
                'primary'    => null,
                'background' => '#f4f6f9',
                'surface'    => '#ffffff',
                'border'     => '#e2e8f0',
                'text'       => '#333333',
                'text_muted' => '#6c757d',
            ],
            'dark' => [
                'primary'    => '#818cf8',
                'background' => '#0f172a',
                'surface'    => '#1e293b',
                'border'     => '#334155',
                'text'       => '#e2e8f0',
                'text_muted' => '#94a3b8',
            ],
        ],

        'auth' => [
            'light' => [
                'background' => '#f4f6f9',
            ],
            'dark' => [
                'background' => '#0f172a',
            ],
        ],
    ],

    'layout' => [
        'favicon'                => '/favicon.ico',
        'dark_mode'              => true,
        'dark_mode_show_on_auth' => false,
        'dark_mode_classes'      => [],
        'dark_mode_dispatch'     => null,
        'dark_mode_callback'     => null,
        'page_transition'    => 'fade',
        'back_to_top'       => true,
        'content_max_width' => null,
        'avatar_resolver'  => null,
    ],

];
