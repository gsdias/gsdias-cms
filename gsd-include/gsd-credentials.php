<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->a(0) == 'logout') {
    $user->logout();
}

if (@$_REQUEST['login'] && !$user->isLogged()) {
    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
        $logged = $user->login($_REQUEST['email'], $_REQUEST['password']);

        if ($logged) {
            $uri = @$_REQUEST['redirect'] ? $_REQUEST['redirect'] : ($site->a(0) == 'admin' ? '/admin' : '/');

            redirect($uri);
        } else {
            $tpl->setvar('FORM_MESSAGES', lang('WRONG_LOGIN'));
            $tpl->setvar('LOGIN_EMAIL', $_REQUEST['email']);
        }
    } else {
        $tpl->setvar('FORM_MESSAGES', lang('CHECK_DATA'));
    }
}
if (@$_REQUEST['reset'] && !$user->isLogged()) {
    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
        $logged = $user->resetpassword($_REQUEST['email']);
        $tpl->setvar('FORM_MESSAGES', 'Password was sent to the email');
    } else {
        $tpl->setvar('FORM_MESSAGES', lang('CHECK_DATA'));
    }
}

define('IS_LOGGED', $user->isLogged());

if (IS_LOGGED) {
    $clientfields = CLIENTPATH.'include/admin/fields'.PHPEXT;
    if (is_file($clientfields)) {
        include_once $clientfields;
    }

    $tpl->setcondition('IS_LOGGED');
    $tpl->setvar('USER_ID', $user->id);

    if ($site->a(0) == 'login') {
        redirect('/');
    }

    foreach($permissions as $permission) {
        $tpl->setcondition('IS_'.strtoupper($permission), $permission == $user->level);
        define('IS_'.strtoupper($permission), $permission == $user->level);
    }

    if ($site->a(0) == 'admin') {
        if ($user->level == 'user') {
            $user->logout();
        }

        $tpl->setvar('USER_NAME', $user->name);
    }
}

if ($uri == '/admin/auth' || $uri == '/admin/auth/') {
    if (IS_LOGGED) {
        redirect('/admin');
    }
    $site->startpoint = 'admin/login';
}

if ($uri == '/admin/reset') {
    if (IS_LOGGED) {
        redirect('/admin');
    }
    $site->startpoint = 'admin/login';
}

if ($uri == '/login' || $uri == '/login/') {
    $site->startpoint = 'index';
    $site->startmain = 'login';
}
