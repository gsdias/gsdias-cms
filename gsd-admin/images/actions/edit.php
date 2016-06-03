<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if (@$_REQUEST['save']) {
    $mysql->reset()
        ->select('extension, width, height, size')
        ->from('images')
        ->where('iid = ?')
        ->values($site->arg(2))
        ->exec();

    $image = $mysql->singleline();

    $_REQUEST['extension'] = $image->extension;
    $_REQUEST['width'] = $image->width;
    $_REQUEST['height'] = $image->height;
    $_REQUEST['size'] = $image->size;
    
    if ($_FILES['asset']['error'] == 0) {
        removefile(ASSETPATH.'images/'.$site->arg(2).'.'.$image->extension);

        $name = explode('.', $_FILES['asset']['name']);
        $extension = end($name);

        $size = getimagesize($_FILES['asset']['tmp_name']);

        $_REQUEST['extension'] = $extension;
        $_REQUEST['width'] = $size[0];
        $_REQUEST['height'] = $size[1];
        $_REQUEST['size'] = round(filesize($_FILES['asset']['tmp_name']) / 1000, 0).'KB';

        $file = savefile($_FILES['asset'], sprintf('%simages/', ASSETPATH), null, null, $site->arg(2));
    }

    $result = $csection->edit();

    if (!$csection->showErrors(lang('LANG_IMAGE_ERROR'))) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_IMAGE_SAVED'), $_REQUEST['name'])));

        redirect('/admin/'.$site->arg(1));
    }
}
