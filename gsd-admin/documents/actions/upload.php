<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $allowed = array(
        'application/msword',
        'application/msexcel',
        'application/pdf',
        'application/vnd.oasis.opendocument.text',
        'application/xml',
        'application/x-gzip',
        'application/zip',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'video/mp4',
    );

    $finfo = new finfo(FILEINFO_MIME);

    $type = $finfo->file($_FILES['asset']['tmp_name']);

    $valid = in_array(explode(';', $type)[0], $allowed);

    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);

    $_REQUEST['extension'] = $extension;
    $_REQUEST['size'] = round(filesize($_FILES['asset']['tmp_name']) / 1000, 0).'KB';

    if ($valid) {
        $result = $csection->add();

        if (!$csection->showErrors(lang('LANG_DOCUMENT_ERROR'))) {
            $id = $mysql->lastInserted();

            $file = savefile($_FILES['asset'], ASSETPATH.'documents/', null, null, $id);
            $_SESSION['message'] = sprintf(lang('LANG_DOCUMENT_UPLOADED'), $_REQUEST['name']);

            redirect('/admin/'.$site->arg(1));
        }
    } else {
        $tpl->setvar('ERRORS', lang('LANG_DOCUMENT_FORMAT'));
        $tpl->setcondition('ERRORS');
    }
}
