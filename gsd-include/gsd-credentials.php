<?php

if (@$_REQUEST['login'] && !$user->isLogged()) {

    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {

        $logged = $user->login($_REQUEST['email'], $_REQUEST['password']);
        
        if ($logged) {
            $uri = @$_REQUEST['redirect'] ? $_REQUEST['redirect'] : ($site->arg(0) == 'admin' ? '/admin' : '/');

            header('location: ' . $uri);
            exit;
        } else {
            $tpl->setvar('FORM_MESSAGES', 'Login errado');
            $tpl->setvar('LOGIN_EMAIL', $_REQUEST['email']);
        }
    } else {
        $tpl->setvar('FORM_MESSAGES', 'Verifique os dados.');
    }

}

if ($user->isLogged()) {

    if ($site->arg(0) == 'login') {
        header('location: /');
        exit;
    }
    
    if ($site->arg(0) == 'admin') {
        if ($user->level != 'user') {
            if ($user->level == 'admin') {
                $tpl->setcondition('IS_ADMIN');

            }
            $tpl->setcondition('IS_LOGGED');
            define('IS_ADMIN', $user->level == 'admin');
            define('IS_LOGGED', 1);
            $tpl->setvar('USER_NAME', $user->name);
            $tpl->setvar('USER_ID', $user->id);
        } else {
            $user->logout();
        }
    } else {
    
        $tpl->setcondition('IS_LOGGED');
        define('IS_LOGGED', 1);
        $tpl->setvar('USER_ID', $user->id);

    }
    $tpl->setVar('SCRIPT', sprintf('server.userId = "%s";', $user->id));
    
} else {
    define('IS_LOGGED', 0);
}

if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
    if (IS_LOGGED) {
        header('location: /admin');
        exit;
    }
    $startpoint = 'admin/login';
}
