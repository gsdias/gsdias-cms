<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
    $defaultfields = array('name', 'description', 'tags');

    if ($_FILES['asset']['error'] == 0) {
        $mysql->reset()
            ->select('extension')
            ->from('images')
            ->where('iid = ?')
            ->values($site->arg(2))
            ->exec();

        $image = $mysql->singleline();

        removefile(ASSETPATH.'images/'.$site->arg(2).'.'.$image->extension);

        $name = explode('.', $_FILES['asset']['name']);
        $extension = end($name);

        $size = getimagesize($_FILES['asset']['tmp_name']);

        array_push($defaultfields, 'extension', 'width', 'height', 'size');

        $_REQUEST['extension'] = $extension;
        $_REQUEST['width'] = $size[0];
        $_REQUEST['height'] = $size[1];
        $_REQUEST['size'] = round(filesize($_FILES['asset']['tmp_name']) / 1000, 0).'KB';

        $file = savefile($_FILES['asset'], sprintf('%simages/', ASSETPATH), null, null, $site->arg(2));
    }

    $result = $csection->edit($defaultfields);

    $_SESSION['message'] = lang('LANG_IMAGE_SAVED');

    header('Location: /admin/images', true, 302);
    exit;
}
