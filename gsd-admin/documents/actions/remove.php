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
if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->reset()
        ->select('extension, name')
        ->from('documents')
        ->where('did = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $document = $mysql->singleline();

    removefile(ASSETPATH.'documents/'.$site->arg(2).'.'.$document->extension);

    $result = $csection->remove();

    if (!$result['errnum']) {
        $_SESSION['message'] = sprintf(lang('LANG_DOCUMENT_REMOVED'), $document->name);

        header('Location: /admin/documents', true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header('Location: /admin/documents', true, 302);
    exit;
}
