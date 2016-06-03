<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.7
 */
defined('GVALID') or die;
$mysql->reset()
    ->update($site->arg(1))
    ->fields('deleted')
    ->where(sprintf('%sid = ?', substr($section, 0, 1)))
    ->values(array(null, $site->arg(2)))
    ->exec();

if ($mysql->errmsg) {
    $tpl->setarray('ERRORS', array('MSG' => lang('LANG_RECOVER_ERROR')));
    redirect('/admin/'.$site->arg(1));
} else {
    $tpl->setarray('MESSAGES', array('MSG' => '{LANG_RECOVER_DONE}'));
    redirect('/admin/'.$site->arg(1));
}
