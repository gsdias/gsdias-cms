<?php

$iid = explode('_', $site->path[1]);

$mysql->statement('SELECT extension FROM images WHERE iid = :iid;', array(':iid' => $iid[0]));

$asset = sprintf('gsd-assets/images/%d/%d.%s', $iid[0], $iid[0], $mysql->singleresult());

if ($mysql->total) {
    $size = getimagesize($asset);

    $fp = fopen($asset, "rb");
    if ($size && $fp) {
        header("Content-type: {$size['mime']}");
        fpassthru($fp);
        exit;
    }
}