<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if (!IS_LOGGED) {
    if ($site->uri != '/admin/auth' && $site->uri != '/admin/auth/' && $site->uri != '/admin/reset') {
        redirect('/admin/auth?redirect='.urlencode($site->uri));
    } else {
        $site->startpoint = 'login';
        $tpl->setvar('EXTRACLASS', 'login');
    }
} else {
    if ($site->a(2) && !$site->a(3) && is_numeric($site->a(2))) {
        redirect($site->uri.'/details');
    }
    $site->main = $site->a(1) ? $site->a(1) : 'dashboard';
    $site->startpoint = 'index';

    $clientfields = CLIENTPATH.'tpl/admin/_clientaside'.TPLEXT;
    $tpl->setcondition('HASCLIENTASIDE', is_file($clientfields));

    if (!$site->a(1)) {
        include_once 'gsd-admin/dashboard'.PHPEXT;
        $tpl->setvar('DASHBOARD_ACTIVE', 'active');
    } else {
        $afirmative = lang('LANG_YES');
        $negative = lang('LANG_NO');
        
        $tpl->setvar(strtoupper($site->a(1)).'_ACTIVE', 'active');
        if ($site->a(1) == 'settings') {
            $file = 'gsd-admin/settings'.PHPEXT;
        } elseif ($site->a(1) == 'language') {
            $file = 'gsd-admin/language'.PHPEXT;
        } elseif ($site->a(1) == 'update') {
            $file = 'gsd-admin/update'.PHPEXT;
        } elseif ($site->a(3) == 'recover') {
            $file = 'gsd-admin/recover'.PHPEXT;
        } else {
            if (class_exists('\\GSD\\'.$site->a(1)) || class_exists('\\GSD\\Extended\extended'.$site->a(1))) {
                $file = 'gsd-admin/list'.PHPEXT;
            } else {
                $site->main = '404';
                http_response_code(404);
            }
        }
        if (is_file(@$file)) {
            include_once $file;
        }
    }

    if (isset($csection) && !$csection->permission) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_NOPERMISSION')));
        redirect('/admin');
    }
}
