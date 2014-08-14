<?php

if (@$_REQUEST['confirm'] == 'Sim') {
    removefile(ASSETPATH . 'documents/' . $site->arg(2));

    $mysql->statement('DELETE FROM documents WHERE did = ?;', array($site->arg(2)));

    if ($mysql->total) {
        header("Location: /admin/documents", true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/documents", true, 302);
    exit;
}