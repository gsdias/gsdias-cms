<?php

removefile(ASSETPATH . 'images/' . $site->arg(2));

$mysql->statement('DELETE FROM images WHERE iid = ?;', array($site->arg(2)));

if ($mysql->total) {
    header("Location: /admin/images", true, 302);
    exit;
}
