<?php

if (@$_REQUEST['confirm'] == 'Sim') {
    removefile(ASSETPATH . 'images/' . $site->arg(2));

    $mysql->statement('DELETE FROM images WHERE iid = ?;', array($site->arg(2)));

    if ($mysql->total) {
        header("Location: /admin/images", true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == 'Nao') {
    header("Location: /admin/layouts", true, 302);
    exit;
}
