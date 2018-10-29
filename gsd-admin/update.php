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

    
$templatefiles = scandir('gsd-locale');

foreach ($templatefiles as $file) {
    if (substr($file, 0, 1) !== '.') {
        $cmslocalepath = ROOTPATH."/gsd-locale/".$file."/LC_MESSAGES/";
        $content = file_get_contents($cmslocalepath."/messages.po");
        $myfile = fopen($cmslocalepath."/messages.ini", "w") or die("Unable to open file!");
        preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

        fwrite($myfile, "[".$file."]\n");

        foreach ($matches as $match) {
            if ($match[1] !== "" && $match[2] !== "") {
                fwrite($myfile, $match[1]." = \"".$match[2]."\"\n");
            }
        }
        fclose($myfile);
        $clientlocalepath = CLIENTPATH."/locale/".$file."/LC_MESSAGES/";
        if (file_exists($clientlocalepath."extended.po")) {
            $content = file_get_contents($clientlocalepath."extended.po");
            $myfile = fopen($clientlocalepath."extended.ini", "w") or die("Unable to open file!");
            preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

            fwrite($myfile, "[".$file."]\n");

            foreach ($matches as $match) {
                if ($match[1] !== "" && $match[2] !== "") {
                    fwrite($myfile, $match[1]." = \"".$match[2]."\"\n");
                }
            }
            fclose($myfile);
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
