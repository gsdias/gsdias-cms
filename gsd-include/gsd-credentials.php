<?php

if (@$_REQUEST['login']) {

    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {

        $logged = $user->login($_REQUEST['email'], $_REQUEST['password']);
        
        if ($logged) {
            header('location: ' . @$_REQUEST['redirect']);
        } else {
            $tpl->setvar('FORM_MESSAGES', 'Login errado');
            $tpl->setvar('LOGIN_EMAIL', $_REQUEST['email']);
        }
    } else {
        $tpl->setvar('FORM_MESSAGES', 'Verifique os dados.');
    }

}

if ($user->isLogged()) {

    if ($path[0] == 'login') {
        header('location: /');
    }
    
    if ($path[0] == 'admin') {
        if ($user->level != 'user') {
            if ($user->level == 'admin') {
                $tpl->setcondition('IS_ADMIN');
            }
            $tpl->setcondition('IS_LOGGED');
            define('IS_LOGGED', 1);
            $tpl->setvar('USER_NAME', $user->name);
        } else {
            $user->logout();
        }
    } else {
    
        $tpl->setcondition('IS_LOGGED');
        define('IS_LOGGED', 1);
        $tpl->setvar('USER_NAME', $user->name);

    }
    
} else {
    define('IS_LOGGED', 0);
}

if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
    if (IS_LOGGED) {
        header('location: /admin');
    }
    $startpoint = 'admin/login';
}
