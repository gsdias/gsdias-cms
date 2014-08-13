<?php

if (!IS_ADMIN) {
    
    $_SESSION['error'] = 'Nao tem permissao para apagar paginas.';
    
    header("Location: /admin/pages", true, 302);
    exit;
}

$mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($site->arg(2)));

$currenturl = $mysql->singleresult();

$mysql->statement('DELETE FROM redirect WHERE `destination` = ?;', array($currenturl));

$mysql->statement('DELETE FROM pages WHERE pid = ?;', array($site->arg(2)));

if ($mysql->errnum) {
    
    $tpl->setvar('ERRORS', 'Houve um problema ao apagar a pagina.');
    $tpl->setcondition('ERRORS');

} else {

    $_SESSION['message'] = 'Pagina apagada.';

    header("Location: /admin/pages", true, 302);
    exit;

}
