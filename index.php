<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
define('ROOTPATH', __DIR__.'/');
define('GVALID', 1);

include_once ROOTPATH.'gsd-include/gsd-config.php';

$content = file_get_contents("gsd-locale/en_GB/LC_MESSAGES/messages.po");
$myfile = fopen("gsd-locale/en_GB/LC_MESSAGES/messages.ini", "w") or die("Unable to open file!");
preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

fwrite($myfile, "[en_GB]\n");

foreach ($matches as $match) {
    if ($match[1] !== "" && $match[2] !== "") {
        fwrite($myfile, $match[1]." = \"".$match[2]."\"\n");
    }
}
fclose($myfile);

if (!IS_INSTALLED) {
    if ($site->uri != '/admin') {
        redirect('/admin');
    }

    $site->main = 'STEP1';
    require_once INCLUDEPATH.'gsd-install'.PHPEXT;

    $tpl->includeFiles('MAIN', $site->main);
    $tpl->setFile($site->startpoint);
} elseif ($site->a(0) == 'assets') {
    require_once INCLUDEPATH.'gsd-assets'.PHPEXT;
} else {
    require_once INCLUDEPATH.'gsd-credentials'.PHPEXT;

    if (!$site->isFrontend) {
        require_once ADMINPATH.'index'.PHPEXT;
    } else {
        if (is_file(CLIENTPATH.'index'.PHPEXT)) {
            if (!IS_LOGGED && @$site->page->require_auth) {
                redirect('/login?redirect='.urlencode($site->uri));
            }
            require_once CLIENTPATH.'index'.PHPEXT;
        }
    }

    $tpl->includeFiles('MAIN', $site->main);
    $tpl->setFile($site->startpoint);
}

echo $tpl;
