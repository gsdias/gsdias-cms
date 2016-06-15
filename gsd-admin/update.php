<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.6
 */
defined('GVALID') or die;
$files = scandir('gsd-sql/changes');

$update = array();
$startupdate = 0;

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        if (strpos($file, @$site->options['version']->value) !== false) {
            $startupdate = 1;
        }
        if (!$startupdate) {
            continue;
        }
        $sentence = file_get_contents(sprintf('gsd-sql/changes/%s', $file));
        $mysql->statement($sentence);

        if ($mysql->errmsg) {
            $update[] = $mysql->errmsg;
        }
    }
}

if (empty($update)) {
    $mysql->reset()
        ->update('options')
        ->fields(array('value'))
        ->where('name = ?')
        ->values(array($site::VERSION, 'version'))
        ->exec();

    $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_UPDATE_FINISHED')));

    redirect('/admin');
} else {
    foreach($update as $error) {
        $tpl->setarray('ERRORS', array('MSG' => $error));
    }
    redirect('/admin');
}
