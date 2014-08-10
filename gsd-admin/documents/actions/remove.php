<?php

removefile(ASSETPATH . 'images/' . $path[2]);

$mysql->statement('DELETE FROM images WHERE iid = ?;', array($path[2]));

if ($mysql->total) {
    header("Location: /admin/images", true, 302);
    exit;
}
