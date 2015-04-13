<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);

    $size = getimagesize($_FILES['asset']["tmp_name"]);

    $fields = array(
        $_REQUEST['name'],
        $_REQUEST['description'],
        $extension,
        round(filesize($_FILES['asset']["tmp_name"]) / 1000, 0) . 'KB',
        $user->id
    );

    $mysql->statement('INSERT INTO documents (name, description, extension, size, creator) values(?, ?, ?, ?, ?);', $fields);
echo $mysql->errmsg;
    if ($mysql->total) {

        $id = $mysql->lastInserted();

        $file = savefile ($_FILES['asset'], ASSETPATH . 'documents/', null, null, $id);

        header("Location: /admin/documents", true, 302);
        exit;
    }
}
