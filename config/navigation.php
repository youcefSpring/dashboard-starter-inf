<?php

/*
|--------------------------------------------------------------------------
| Sidebar Navigation
|--------------------------------------------------------------------------
|
| Each group renders as a section of the sidebar. Items link to a named
| route; "active" is a route name pattern used to highlight the item, and
| "icon" is any Lucide icon name (register new ones in resources/js/icons.js).
|
*/

return [
    [
        'title' => null,
        'items' => [
            ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
            ['label' => 'Users', 'icon' => 'users', 'route' => 'users.index', 'active' => 'users.*'],
        ],
    ],
    [
        'title' => 'Account',
        'items' => [
            ['label' => 'Settings', 'icon' => 'settings', 'route' => 'settings.edit', 'active' => 'settings.*'],
        ],
    ],
    [
        'title' => 'Developers',
        'items' => [
            ['label' => 'Components', 'icon' => 'blocks', 'route' => 'components'],
        ],
    ],
];
