<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
define('ROOTPATH', dirname(__FILE__).'/');

include_once 'gsd-include/gsd-config.php';

$tpl->setvar('HTML_CLASS', 'gsd');

if (!IS_INSTALLED) {
    $site->main = 'STEP1';
    require_once 'gsd-install'.PHPEXT;

    $tpl->includeFiles('MAIN', $site->main);
    $tpl->setFile($site->startpoint);
} elseif ($site->arg(1) == 'assets') {
    require_once 'gsd-assets'.PHPEXT;
} else {
    require_once INCLUDEPATH.'gsd-credentials'.PHPEXT;

    if ($site->arg(0) == 'admin') {
        require_once 'gsd-admin/index.php';
    }

    if (is_file(ROOTPATH.'gsd-frontend/index.php') && $site->arg(0) != 'admin') {
        if (!IS_LOGGED && @$site->page->require_auth) {
            header('location: /login?redirect='.urlencode($site->uri));
            exit;
        }
        require_once ROOTPATH.'gsd-frontend/index.php';
    }

    $tpl->includeFiles('MAIN', $site->main);
    $tpl->setFile($site->startpoint);
}

echo $tpl;
