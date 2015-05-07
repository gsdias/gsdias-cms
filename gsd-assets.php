<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$site->arg(2) == 'images') {
    $iid = is_numeric(@$site->arg(3)) ? $site->arg(3) : 0;
    $name = explode('.', @$site->arg(3));

    if (sizeof($name) === 2 || $iid) {
        if (sizeof($name) === 2) {
            $mysql->reset()
                ->select('iid, extension')
                ->from('images')
                ->where('name = ?')
                ->where('AND extension = ?')
                ->values(array($name[0], @$name[1]))
                ->exec();
        } else {
            $mysql->statement('SELECT iid, extension FROM images WHERE iid = ?;', array($iid));
        }

        $image = $mysql->singleline();

        $asset = sprintf(ASSETPATH . 'images/%d.%s', @$image->iid, @$image->extension);

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
}
