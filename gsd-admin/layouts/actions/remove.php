<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN) {
    $_SESSION['error'] = '{LANG_LAYOUT_NOPERMISSION}';
    header("Location: /admin/layouts", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->statement('DELETE FROM layouts WHERE lid = ?;', array($site->arg(2)));

    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', '{LANG_LAYOUT_RELATED}');
        $tpl->setcondition('ERRORS');

    } else {

        $_SESSION['message'] = '{LANG_LAYOUT_REMOVED}';

        header("Location: /admin/layouts", true, 302);
        exit;

    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header("Location: /admin/layouts", true, 302);
    exit;
}
