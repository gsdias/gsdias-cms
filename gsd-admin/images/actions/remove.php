<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->statement('SELECT extension, name FROM images WHERE iid = ?;', array($site->arg(2)));
    $image = $mysql->singleline();

    removefile(ASSETPATH . 'images/' . $site->arg(2) . '.' . $image->extension);

    $mysql->statement('DELETE FROM images WHERE iid = ?;', array($site->arg(2)));

    if ($mysql->total) {

        $_SESSION['message'] = sprintf(lang('LANG_IMAGE_REMOVED'), $image->name);

        header("Location: /admin/images", true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header("Location: /admin/images", true, 302);
    exit;
}
