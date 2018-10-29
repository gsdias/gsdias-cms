<?php

$tpl->setarray('MENU_ITEMS', array(
    array(
        'NAME' => 'LANG_DASHBOARD',
        'URL' => '/admin/',
        'ICON' => 'fa-dashboard',
        'PERMISSION' => true,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_LAYOUTS',
        'URL' => '/admin/layouts',
        'ICON' => 'fa-tasks',
        'PERMISSION' => IS_ADMIN,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_PAGES',
        'URL' => '/admin/pages',
        'ICON' => 'fa-files-o',
        'PERMISSION' => IS_ADMIN || IS_EDITOR,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_IMAGES',
        'URL' => '/admin/images',
        'ICON' => 'fa-camera-retro',
        'PERMISSION' => IS_ADMIN || IS_EDITOR,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_DOCUMENTS',
        'URL' => '/admin/documents',
        'ICON' => 'fa-briefcase',
        'PERMISSION' => IS_ADMIN || IS_EDITOR,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_USERS',
        'URL' => '/admin/users',
        'ICON' => 'fa-users',
        'PERMISSION' => IS_ADMIN,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_LANGUAGE',
        'URL' => '/admin/language',
        'ICON' => 'fa-language',
        'PERMISSION' => IS_ADMIN || IS_EDITOR,
        'ACTIVE' => '/admin/' . $site->a(1)
    ),
    array(
        'NAME' => 'LANG_SETTINGS',
        'URL' => '/admin/settings',
        'ICON' => 'fa-cogs',
        'PERMISSION' => IS_ADMIN,
        'ACTIVE' => '/admin/' . $site->a(1)
    )
));