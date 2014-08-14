<?php

if (!IS_ADMIN) {
    $_SESSION['error'] = 'Não tem permissão para remover utilizadores.';
    header("Location: /admin/users", true, 302);
    exit;
}

if ($site->arg(2) == 1) {
    $_SESSION['error'] = 'Não pode remover o utilizador padrão.';
    header("Location: /admin/users", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == 'Sim') {
    $mysql->statement('DELETE FROM users WHERE uid = ?;', array($site->arg(2)));
    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', 'Houve um erro. Tente mais tarde.');
        $tpl->setcondition('ERRORS');
    } else {

        $_SESSION['message'] = 'Utilizador apagado.';

        header("Location: /admin/users", true, 302);
        exit;
    }

}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/users", true, 302);
    exit;
}
