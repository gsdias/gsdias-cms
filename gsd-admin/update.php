<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.5.1
 */

$files = scandir('gsd-sql/changes');

$update = array();

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $sentence = file_get_contents(sprintf('gsd-sql/changes/%s', $file));
        $mysql->statement('SET foreign_key_checks = 0;'.$sentence.'SET foreign_key_checks = 1;');

        if ($mysql->executed) {
            $update[] = 1;
        } else {
            $update[] = 0;
        }
    }
}

if (in_array(1, $update)) {
    $mysql->reset()
        ->update('options')
        ->fields(array('value'))
        ->where('name = ?')
        ->values(array($site::VERSION, 'gsd-version'))
        ->exec();

    $_SESSION['message'] = lang('LANG_UPDATE_FINISHED');
    $tpl->setcondition('MESSAGES');

    redirect('/admin');
}
