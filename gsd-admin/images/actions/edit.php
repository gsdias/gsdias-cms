<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {

    $defaultfields = array(
        $_REQUEST['name'],
        $_REQUEST['description'],
        $_REQUEST['tags'],
        $site->arg(2)
    );

    $mysql->statement('UPDATE images SET name = ?, description = ?, tags = ? WHERE iid = ?;', $defaultfields);

    $mysql->statement('SELECT * FROM images WHERE iid = ?;', $site->arg(2));

    $image = $mysql->singleline();

    if ($_FILES['asset']['error'] == 0) {

        removefile(ASSETPATH . 'images/' . $site->arg(2) . '.' . $image['extension']);

        $name = explode('.', $_FILES['asset']['name']);
        $extension = end($name);

        $size = getimagesize($_FILES['asset']["tmp_name"]);

        $fields = array(
            $extension,
            $size[0],
            $size[1],
            round(filesize($_FILES['asset']["tmp_name"]) / 1000, 0) . 'KB',
            $site->arg(2)
        );

        $file = savefile ($_FILES['asset'], sprintf('%simages/', ASSETPATH), null, null, $site->arg(2));

        $mysql->statement('UPDATE images SET extension = ?, width = ?, height = ?, size = ? WHERE iid = ?;', $fields);
    }
    header("Location: /admin/images", true, 302);
    exit;
}
