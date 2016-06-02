<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
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

//    removefile(ASSETPATH.'images/'.$site->arg(2).'.'.$image->extension);

    $result = $csection->remove();

    if (!$result['errnum']) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_IMAGE_REMOVED'), $image->name)));

        redirect('/admin/'.$site->arg(1));
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    redirect('/admin/'.$site->arg(1));
}
