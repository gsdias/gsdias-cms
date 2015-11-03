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
if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->reset()
        ->select('extension, name')
        ->from('images')
        ->where('iid = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $image = $mysql->singleline();

    removefile(ASSETPATH.'images/'.$site->arg(2).'.'.$image->extension);

    $result = $csection->remove();

    if (!$result['errnum']) {
        $_SESSION['message'] = sprintf(lang('LANG_IMAGE_REMOVED'), $image->name);

        header('Location: /admin/images', true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header('Location: /admin/images', true, 302);
    exit;
}
