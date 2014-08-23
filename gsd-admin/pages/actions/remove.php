<?php

if (!IS_ADMIN) {
    
    $_SESSION['error'] = '{LANG_PAGE_NOPERMISSION}';
    
    header("Location: /admin/pages", true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == 'Sim') {
    $mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($site->arg(2)));

    $currenturl = $mysql->singleresult();

    $mysql->statement('DELETE FROM redirect WHERE `destination` = ?;', array($currenturl));

    $mysql->statement('DELETE FROM pages WHERE pid = ?;', array($site->arg(2)));

    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', '{LANG_PAGE_ERROR}');
        $tpl->setcondition('ERRORS');

    } else {

        $_SESSION['message'] = '{LANG_PAGE_REMOVED}';

        header("Location: /admin/pages", true, 302);
        exit;

    }
}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/pages", true, 302);
    exit;
}
