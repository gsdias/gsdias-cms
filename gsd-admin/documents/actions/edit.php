<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
    $defaultfields = array(
        $_REQUEST['name'],
        $_REQUEST['description'],
        $site->arg(2),
    );

    $mysql->reset()
        ->update('documents')
        ->fields(array('name', 'description'))
        ->where('did = ?')
        ->values($defaultfields)
        ->exec();

    if ($_FILES['asset']['error'] == 0) {
        removefile(ASSETPATH.'images/'.$site->arg(2));

        $name = explode('.', $_FILES['asset']['name']);
        $extension = end($name);

        $size = getimagesize($_FILES['asset']['tmp_name']);

        $fields = array(
            $extension,
            $size[0],
            $size[1],
            round(filesize($_FILES['asset']['tmp_name']) / 1000, 0).'KB',
            $site->arg(2),
        );

        $file = savefile($_FILES['asset'], ASSETPATH.'images/'.$site->arg(2).'/', null, null, $site->arg(2));

        $mysql->reset()
        ->update('documents')
        ->fields(array('extension', 'width', 'width', 'height', 'size'))
        ->where('did = ?')
        ->values($fields)
        ->exec();
    }
    header('Location: /admin/documents', true, 302);
    exit;
}
