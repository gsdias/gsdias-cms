<?php

if (!IS_LOGGED) {
    if ($uri != '/admin/auth' && $uri != '/admin/auth/') {
        header('location: /admin/auth?redirect=' . urlencode($uri));
    } else {
        $startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if ($path[2] && !@$path[3] && is_numeric($path[2])) {
        header('location: ' . $uri . '/details');
    }
    $startpoint = 'index';
    $main = $path[1] ? $path[1] : 'dashboard';
    
    if (!$path[1]) {
        include_once('gsd-admin/dashboard' . PHPEXT);
    } else {
        if ($path[1] == 'settings') {
            $file = 'gsd-admin/settings' . PHPEXT;
        } else {
            $file = 'gsd-admin/list' . PHPEXT;
            
        }
        if (is_file($file)) {
            include_once($file);
        }
    }
    $file = CLIENTPATH . 'include/admin/' . $path[1] . PHPEXT;
    
    if (is_file($file)) {
        include_once($file);
    }
}