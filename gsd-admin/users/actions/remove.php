<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->a(2) == 1) {
    $tpl->setarray('ERRORS', array('MSG' => lang('LANG_USER_DEFAULT')));
    redirect('/admin/'.$site->a(1));
}

if ($site->p('confirm') == $afirmative) {
    $mysql->reset()
        ->select('name')
        ->from('users')
        ->where('uid = ?')
        ->values(array($site->a(2)))
        ->exec();

    $result = $mysql->singleline();

    $name = $result->name;

    $result = $csection->remove();

    if ($result['total'] === 0) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_USER_ERROR')));
        $tpl->setcondition('ERRORS');
    } else {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_USER_REMOVED'), $name)));

        redirect('/admin/'.$site->a(1));
    }
}

if ($site->p('confirm') == $negative) {
    redirect('/admin/'.$site->a(1));
}
