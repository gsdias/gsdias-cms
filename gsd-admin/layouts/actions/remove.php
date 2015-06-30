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
    $_SESSION['error'] = lang('LANG_LAYOUT_NOPERMISSION');
    header('Location: /admin/layouts', true, 302);
    exit;
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->reset()
        ->select('name')
        ->from('layouts')
        ->where('lid = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $result = $mysql->singleline();

    $name = $result->name;

    $result = $csection->remove();

    if ($result['errnum']) {
        $tpl->setvar('ERRORS', lang('LANG_LAYOUT_RELATED'));
        $tpl->setcondition('ERRORS');
    } else {
        $_SESSION['message'] = sprintf(lang('LANG_LAYOUT_REMOVED'), $name);

        header('Location: /admin/layouts', true, 302);
        exit;
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header('Location: /admin/layouts', true, 302);
    exit;
}
