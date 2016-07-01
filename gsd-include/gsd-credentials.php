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

if ($site->p('login') && !$user->isLogged()) {
    if (filter_var($site->p('email'), FILTER_VALIDATE_EMAIL)) {
        $logged = $user->login($site->p('email'), $site->p('password'));

        if ($logged) {
            $uri = $site->p('redirect') ? $site->p('redirect') : '/';

            redirect($uri);
        } else {
            $tpl->setvar('FORM_MESSAGES', lang('WRONG_LOGIN'));
            $tpl->setvar('LOGIN_EMAIL', $site->p('email'));
        }
    } else {
        $tpl->setvar('FORM_MESSAGES', lang('CHECK_DATA'));
    }
}
if ($site->p('reset') && !$user->isLogged()) {
    if (filter_var($site->p('email'), FILTER_VALIDATE_EMAIL)) {
        $logged = $user->resetpassword($site->p('email'));
        $tpl->setvar('FORM_MESSAGES', 'Password was sent to the email');
    } else {
        $tpl->setvar('FORM_MESSAGES', lang('CHECK_DATA'));
    }
}

define('IS_LOGGED', $user->isLogged());

if (IS_LOGGED) {
    $tpl->setcondition('IS_LOGGED');
    $tpl->setvar('USER_ID', $user->id);

    if ($site->a(0) == 'login') {
        redirect('/');
    }

    if ($site->a(0) == 'admin') {
        if ($user->level == 'user') {
            $user->logout();
        }

        $tpl->setvar('USER_NAME', $user->name);
        $tpl->setvar('USER_NOTIFICATIONS_UNREAD', sizeof($user->notifications->unread));
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

if ($site->a(0) == 'login') {
    $site->startpoint = 'index';
    $site->startmain = 'login';
}

foreach($GSDConfig->permissions as $permission) {
    $tpl->setcondition('IS_'.strtoupper($permission), $permission == $user->level);
    define('IS_'.strtoupper($permission), $permission == $user->level);
}
