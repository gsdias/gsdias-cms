<?php

if (@$site->arg(1) == 'images') {
    $iid = explode('_', @$site->arg(2));

    $mysql->statement('SELECT extension FROM images WHERE iid = :iid;', array(':iid' => $iid[0]));

    $asset = sprintf('gsd-assets/images/%d.%s', $iid[0], $mysql->singleresult());

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
