<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN && !IS_EDITOR) {
    header("Location: /admin/" . $site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {

    $defaultfields = array('name', 'description', 'extension', 'size', 'creator');

    $fields = array('creator');

    $values = array($user->id);

    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);

    $_REQUEST['extension'] = $extension;
    $_REQUEST['size'] = round(filesize($_FILES['asset']["tmp_name"]) / 1000, 0) . 'KB';

    $result = $csection->add($defaultfields, $fields, $values);

    if ($mysql->total) {

        $id = $mysql->lastInserted();

        $file = savefile ($_FILES['asset'], ASSETPATH . 'documents/', null, null, $id);

        header("Location: /admin/" . $site->arg(1), true, 302);
        exit;
    }
}
