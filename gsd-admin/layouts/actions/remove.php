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
        ->select('name')
        ->from('layouts')
        ->where('lid = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $result = $mysql->singleline();

    $name = $result->name;

    $result = $csection->remove();

    if ($result['errnum']) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LAYOUT_RELATED')));
        $tpl->setcondition('ERRORS');
    } else {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_LAYOUT_REMOVED'), $name)));

        redirect('/admin/'.$site->arg(1));
    }
}

if ($site->p('confirm') == $negative) {
    redirect('/admin/'.$site->arg(1));
}
