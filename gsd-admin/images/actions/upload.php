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
if (!$csection->permission) {
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    $defaultfields = array('name', 'description', 'tags', 'extension', 'width', 'height', 'size');

    $fields = array('creator');

    $values = array($user->id);

    $size = getimagesize($_FILES['asset']['tmp_name']);

    $valid = is_array($size);

    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);

    $_REQUEST['extension'] = $extension;
    $_REQUEST['width'] = $size[0];
    $_REQUEST['height'] = $size[1];
    $_REQUEST['size'] = round(filesize($_FILES['asset']['tmp_name']) / 1000, 0).'KB';

    if ($valid) {
        $result = $csection->add($defaultfields, $fields, $values);

        if ($result['errnum']) {
            $tpl->setvar('ERRORS', lang('LANG_IMAGE_ERROR'));
            $tpl->setcondition('ERRORS');
        } else {
            $id = $mysql->lastInserted();

            $file = savefile($_FILES['asset'], ASSETPATH.'images/', null, null, $id);

            header('Location: /admin/'.$site->arg(1), true, 302);
            exit;
        }
    } else {
        $tpl->setvar('ERRORS', lang('LANG_IMAGE_FORMAT'));
        $tpl->setcondition('ERRORS');
    }
}
