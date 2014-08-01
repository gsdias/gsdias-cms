<?php

if (!IS_LOGGED) {
    if ($uri != '/admin/auth' && $uri != '/admin/auth/') {
        header('location: /admin/auth?redirect=' . urlencode($uri));
    } else {
        $startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
        //header('location: /admin');
    }
    $startpoint = 'index';
    $main = $path[1] ? $path[1] : 'dashboard';
    if (is_file('gsd-admin/' . $path[1] . PHPEXT)) {
        include_once('gsd-admin/' . $path[1] . PHPEXT);
    }
    if (!$path[1]) {
        include_once('gsd-admin/dashboard' . PHPEXT);
    }
}