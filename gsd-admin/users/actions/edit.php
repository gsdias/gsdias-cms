<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {

    $result = $csection->edit();

    if (!$csection->showErrors(lang('LANG_USER_ALREADY_EXISTS'))) {
        $_SESSION['message'] = sprintf(lang('LANG_USER_SAVED'), $_REQUEST['name']);

        if ($result['id'] == $user->id) {
            $user->locale = $_REQUEST['locale'];
        }

        redirect('/admin/'.$site->arg(1));
    }
}
