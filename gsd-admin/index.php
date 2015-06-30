<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!IS_LOGGED) {
    if ($site->uri != '/admin/auth' && $site->uri != '/admin/auth/') {
        header('location: /admin/auth?redirect='.urlencode($site->uri));
        exit;
    } else {
        $site->startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if ($site->arg(2) && !$site->arg(3) && is_numeric($site->arg(2))) {
        header('location: '.$site->uri.'/details');
        exit;
    }
    $site->main = $site->arg(1) ? $site->arg(1) : 'dashboard';
    $site->startpoint = 'index';

    $clientfields = CLIENTPATH.'include/admin/fields'.PHPEXT;
    if (is_file($clientfields)) {
        include_once $clientfields;
    }

    if (!$site->arg(1)) {
        include_once 'gsd-admin/dashboard'.PHPEXT;
        $tpl->setvar('DASHBOARD_ACTIVE', 'active');
    } else {
        $afirmative = lang('LANG_YES');
        $negative = lang('LANG_NO');
        $file = '';
        $tpl->setvar(strtoupper($site->arg(1)).'_ACTIVE', 'active');
        if ($site->arg(1) == 'settings') {
            $file = 'gsd-admin/settings'.PHPEXT;
        } elseif ($site->arg(1) == 'language') {
            $file = 'gsd-admin/language'.PHPEXT;
        } else {
            if (class_exists('\\GSD\\'.$site->arg(1)) || class_exists('\\GSD\\Extended\extended'.$site->arg(1))) {
                $file = 'gsd-admin/list'.PHPEXT;
            } else {
                $site->main = '404';
                http_response_code(404);
            }
        }
        if ($file && is_file($file)) {
            include_once $file;
        }
    }

    if (isset($csection) && !$csection->permission) {
        $_SESSION['error'] = lang('LANG_NOPERMISSION');
        header('Location: /admin', true, 302);
        exit;
    }
}
