<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.2
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['login'] && !$user->isLogged()) {
    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
        $logged = $user->login($_REQUEST['email'], $_REQUEST['password']);

        if ($logged) {
            $uri = @$_REQUEST['redirect'] ? $_REQUEST['redirect'] : ($site->arg(0) == 'admin' ? '/admin' : '/');

            header('location: '.$uri);
            exit;
        } else {
            $tpl->setvar('FORM_MESSAGES', lang('WRONG_LOGIN'));
            $tpl->setvar('LOGIN_EMAIL', $_REQUEST['email']);
        }
    } else {
        $tpl->setvar('FORM_MESSAGES', lang('CHECK_DATA'));
    }
}

define('IS_LOGGED', $user->isLogged());
define('IS_ADMIN', $user->level == 'admin');
define('IS_EDITOR', $user->level == 'editor');

if (IS_LOGGED) {
    $tpl->setcondition('IS_LOGGED');
    $tpl->setvar('USER_ID', $user->id);

    if ($site->arg(0) == 'login') {
        header('location: /');
        exit;
    }

    if (IS_ADMIN) {
        $tpl->setcondition('IS_ADMIN');
    }

    if ($site->arg(0) == 'admin') {
        if ($user->level == 'user') {
            $user->logout();
        }

        $tpl->setvar('USER_NAME', $user->name);
    }
}

if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
    if (IS_LOGGED) {
        header('location: /admin');
        exit;
    }
    $site->startpoint = 'admin/login';
}
