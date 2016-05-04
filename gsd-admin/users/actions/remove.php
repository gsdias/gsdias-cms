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

if ($site->arg(2) == 1) {
    $_SESSION['error'] = lang('LANG_USER_DEFAULT');
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->reset()
        ->select('name')
        ->from('users')
        ->where('uid = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $result = $mysql->singleline();

    $name = $result->name;

    $result = $csection->remove();

    if ($result['errnum']) {
        $tpl->setvar('ERRORS', lang('LANG_USER_ERROR'));
        $tpl->setcondition('ERRORS');
    } else {
        $_SESSION['message'] = sprintf(lang('LANG_USER_REMOVED'), $name);

        redirect('/admin/'.$site->arg(1));
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    redirect('/admin/'.$site->arg(1));
}
