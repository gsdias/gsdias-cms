<?php

if (@$_REQUEST['confirm'] == 'Sim') {
    $mysql->statement('SELECT extension FROM images WHERE iid = ?;', array($site->arg(2)));
    $image = $mysql->singleline();

    removefile(ASSETPATH . 'images/' . $site->arg(2) . '.' . $image['extension']);

    $mysql->statement('DELETE FROM images WHERE iid = ?;', array($site->arg(2)));

    if ($mysql->total) {
        header("Location: /admin/images", true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/images", true, 302);
    exit;
}
