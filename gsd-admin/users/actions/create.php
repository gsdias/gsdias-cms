<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!$csection->permission) {
    $_SESSION['error'] = lang('LANG_USER_NOPERMISSION');
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['save']) {

    $result = $csection->add();

    if (!$csection->showErrors(lang('LANG_USER_ALREADY_EXISTS'))) {
        $_SESSION['message'] = sprintf(lang('LANG_USER_CREATED'), $_REQUEST['name']);

        redirect('/admin/'.$site->arg(1));
    }
}
