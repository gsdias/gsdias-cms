<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN) {
    $_SESSION['error'] = '{LANG_USER_NOPERMISSION}';
    header("Location: /admin/users", true, 302);
    exit;
}

if ($site->arg(2) == 1) {
    $_SESSION['error'] = '{LANG_USER_DEFAULT}.';
    header("Location: /admin/users", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->statement('SELECT name FROM users WHERE uid = ?;', array($site->arg(2)));

    $result = $mysql->singleline();

    $name = $result->name;

    $mysql->statement('DELETE FROM users WHERE uid = ?;', array($site->arg(2)));
    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', '{LANG_USER_ERROR}');
        $tpl->setcondition('ERRORS');
    } else {

        $_SESSION['message'] = sprintf(lang('{LANG_USER_REMOVED}'), $name);

        header("Location: /admin/users", true, 302);
        exit;
    }

}

if (@$_REQUEST['confirm'] == $negative) {
    header("Location: /admin/users", true, 302);
    exit;
}
