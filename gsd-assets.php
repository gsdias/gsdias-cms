<?php

$iid = explode('_', $path[2]);

$mysql->statement('SELECT extension FROM images WHERE iid = :iid;', array(':iid' => $iid[0]));

$asset = sprintf('gsd-assets/images/%s%s', $iid[0], $mysql->singleresult());

if ($mysql->total) {
    $size = getimagesize($asset);

    $fp = fopen($asset, "rb");
    if ($size && $fp) {
        header("Content-type: {$size['mime']}");
        fpassthru($fp);
        exit;
    }
}