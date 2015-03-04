<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['confirm'] == $affirmative) {
    removefile(ASSETPATH . 'documents/' . $site->arg(2));

    $mysql->statement('DELETE FROM documents WHERE did = ?;', array($site->arg(2)));

    if ($mysql->total) {
        header("Location: /admin/documents", true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header("Location: /admin/documents", true, 302);
    exit;
}
