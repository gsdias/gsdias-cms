<?php

if (!IS_LOGGED) {
    if ($site->uri != '/admin/auth' && $site->uri != '/admin/auth/') {
        header('location: /admin/auth?redirect=' . urlencode($site->uri));
    } else {
        $startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if (@$site->path[2] && !@$site->path[3] && is_numeric(@$site->path[2])) {
        header('location: ' . $site->uri . '/details');
    }
    $startpoint = 'index';
    $main = @$site->path[1] ? @$site->path[1] : 'dashboard';
    
    if (!@$site->path[1]) {
        include_once('gsd-admin/dashboard' . PHPEXT);
    } else {
        if ($site->path[1] == 'settings') {
            $file = 'gsd-admin/settings' . PHPEXT;
        } else {
            $file = 'gsd-admin/list' . PHPEXT;
            
        }
        if (is_file($file)) {
            include_once($file);
        }
    }
    $file = CLIENTPATH . 'include/admin/' . @$site->path[1] . PHPEXT;
    
    if (is_file($file)) {
        include_once($file);
    }
}