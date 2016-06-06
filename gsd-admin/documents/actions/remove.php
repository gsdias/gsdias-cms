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
        ->from('documents')
        ->where('did = ?')
        ->values(array($site->a(2)))
        ->exec();

    $document = $mysql->singleline();

//    removefile(ASSETPATH.'documents/'.$site->a(2).'.'.$document->extension);

    $result = $csection->remove();

    if (!$result['errnum']) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_DOCUMENT_REMOVED'), $document->name)));

        redirect('/admin/'.$site->a(1));
    }
}

if ($site->p('confirm') == $negative) {
    redirect('/admin/'.$site->a(1));
}
