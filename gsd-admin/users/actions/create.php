<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->p('save')) {
    $result = $csection->add();

    if (!$csection->showErrors(lang('LANG_USER_ALREADY_EXISTS'))) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_USER_CREATED'), $site->p('name'))));

        redirect('/admin/'.$site->a(1));
    }
}
