<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->p('confirm') == $afirmative) {
    $mysql->reset()
        ->select('extension, name')
        ->from('images')
        ->where('iid = ?')
        ->values(array($site->a(2)))
        ->exec();

    $image = $mysql->singleline();

//    removefile(ASSETPATH.'images/'.$site->a(2).'.'.$image->extension);

    $result = $csection->remove();

    if (!$result['errnum']) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_IMAGE_REMOVED'), $image->name)));

        redirect('/admin/'.$site->a(1));
    }
}

if ($site->p('confirm') == $negative) {
    redirect('/admin/'.$site->a(1));
}
