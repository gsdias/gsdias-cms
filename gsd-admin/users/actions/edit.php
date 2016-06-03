<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if (@$_REQUEST['save']) {
    $result = $csection->edit();

    if (!$csection->showErrors(lang('LANG_USER_ALREADY_EXISTS'))) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_USER_SAVED'), $_REQUEST['name'])));

        if ($result['id'] == $user->id) {
            $user->locale = $_REQUEST['locale'];
        }

        redirect('/admin/'.$site->arg(1));
    }
}
