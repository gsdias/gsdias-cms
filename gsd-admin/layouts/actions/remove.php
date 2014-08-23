<?php

if (!IS_ADMIN) {
    
    $_SESSION['error'] = '{LANG_LAYOUT_NOPERMISSION}';
    
    header("Location: /admin/layouts", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == 'Sim') {
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

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/layouts", true, 302);
    exit;
}
