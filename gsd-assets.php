<?php

if (@$site->arg(1) == 'images') {
    $iid = explode('_', @$site->arg(2));
    $name = explode('.', @$site->arg(2));

    $mysql->statement('SELECT iid, extension FROM images WHERE iid = ? OR (name = ? AND extension = ?);', array($iid[0], $name[0], @$name[1]));

    $image = $mysql->singleline();

    $asset = sprintf('gsd-assets/images/%d.%s', @$image['iid'], @$image['extension']);

    if ($mysql->total) {
        $size = getimagesize($asset);

        $fp = fopen($asset, "rb");
        if ($size && $fp) {
            header("Content-type: {$size['mime']}");
            fpassthru($fp);
            exit;
        }
    } else {
        header("Location: /gsd-image.php", true, 302);
    }
}
