<?php

if (!IS_LOGGED) {
    if ($uri != '/admin/auth' && $uri != '/admin/auth/') {
        header('location: /admin/auth?redirect=' . urlencode($uri));
    } else {
        $startpoint = 'login';
    }
} else {
    if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
        //header('location: /admin');
    }
    $startpoint = 'index';
}