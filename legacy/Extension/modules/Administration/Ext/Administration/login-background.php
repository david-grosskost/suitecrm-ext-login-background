<?php

// Fond de la page de connexion (extension login-background): its section on
// the Administration page, with a link to its settings page.

/** @var array<array-key, array<int, mixed>> $admin_group_header Set by SuiteCRM */
$admin_option_defs = [
    'Administration' => [
        'login_background' => [
            'icon_AdminThemes',
            'LBL_LB_ADMIN_TITLE',
            'LBL_LB_ADMIN_DESC',
            './index.php?module=Administration&action=LoginBackground',
            'admin-themes',
        ],
    ],
];
$admin_group_header[] = [
    'LBL_LB_ADMIN_HEADER',
    '',
    false,
    $admin_option_defs,
    'LBL_LB_ADMIN_HEADER_DESC',
    'module_settings',
];
