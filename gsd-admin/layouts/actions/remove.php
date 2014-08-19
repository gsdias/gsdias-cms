<?php

if (!IS_ADMIN) {
    
    $_SESSION['error'] = 'Nao tem permissao para apagar layouts.';
    
    header("Location: /admin/layouts", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == 'Sim') {
    $mysql->statement('DELETE FROM layouts WHERE lid = ?;', array($site->arg(2)));

    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', 'Houve um problema ao apagar o layout.');
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
