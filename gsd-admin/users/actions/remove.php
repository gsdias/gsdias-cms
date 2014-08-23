<?php

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

if (@$_REQUEST['confirm'] == 'Sim') {
    $mysql->statement('DELETE FROM users WHERE uid = ?;', array($site->arg(2)));
    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', '{LANG_USER_ERROR}');
        $tpl->setcondition('ERRORS');
    } else {

        $_SESSION['message'] = '{LANG_USER_REMOVED}';

        header("Location: /admin/users", true, 302);
        exit;
    }

}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/users", true, 302);
    exit;
}
