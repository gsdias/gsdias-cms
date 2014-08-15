<?php

if (!IS_LOGGED) {
    if ($site->uri != '/admin/auth' && $site->uri != '/admin/auth/') {
        header('location: /admin/auth?redirect=' . urlencode($site->uri));
        exit;
    } else {
        $site->startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if ($site->arg(2) && !$site->arg(3) && is_numeric($site->arg(2))) {
        header('location: ' . $site->uri . '/details');
        exit;
    }
    $site->main = $site->arg(1) ? $site->arg(1) : 'dashboard';
    $site->startpoint = 'index';
    
    $clientfields = CLIENTPATH . 'include/admin/fields' . PHPEXT;
    if (is_file($clientfields)) {
        include_once($clientfields);
    }

    if (!$site->arg(1)) {
        include_once('gsd-admin/dashboard' . PHPEXT);
    } else {
        if ($site->arg(1) == 'settings') {
            $file = 'gsd-admin/settings' . PHPEXT;
        } else {
            $file = 'gsd-admin/list' . PHPEXT;
        }
        if (is_file($file)) {
            include_once($file);
        }
    }
}
