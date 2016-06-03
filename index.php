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

if (!IS_INSTALLED) {
    if ($site->uri != '/admin') {
        redirect('/admin');
    }

    $site->main = 'STEP1';
    require_once INCLUDEPATH.'gsd-install'.PHPEXT;

    $tpl->includeFiles('MAIN', $site->main);
    $tpl->setFile($site->startpoint);
} elseif ($site->arg(0) == 'assets') {
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
