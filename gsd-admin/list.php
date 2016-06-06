<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
$section = $site->a(1);
$id = $site->a(2);
$action = $site->a(3);

//ACTION DETECTED
if ($action) {
    if ($section) {
        $csection = \GSD\sectionFactory::create($section);

        $site->main = sprintf('%s/%s', $section, $action);

        if ($action === 'edit') {
            $site->main = '_fields';
        } elseif ($action === 'remove') {
            $site->main = '_remove';
        }
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $action, PHPEXT);
        if (is_file($file)) {
            include_once $file;
        }

        $file = sprintf('%sinclude/admin/%s/actions/%s%s', CLIENTPATH, $section, $action, PHPEXT);

        if (is_file($file)) {
            include_once $file;
        }

        $csection->getcurrent($id);

        if ($action === 'edit') {
            $csection->generatefields(true);
        }
    }
} else {

    //ID DETECTED
    if ($id) {
        $csection = \GSD\sectionFactory::create($section);

        $csection->generatefields();

        $site->main = sprintf('%s/%s', $section, $id);
        
        if ($id === 'create' || $id === 'upload') {
            $site->main = '_fields';
        }
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $id, PHPEXT);

        if (is_file($file)) {
            include_once $file;
        }

        $file = sprintf('%sinclude/admin/%s/actions/%s%s', CLIENTPATH, $section, $id, PHPEXT);

        if (is_file($file)) {
            include_once $file;
        }

    //LISTING
    } else {
        $numberPerPage = 10;

        $csection = \GSD\sectionFactory::create($section);

        $csection->getlist(array('numberPerPage' => $numberPerPage, 'page' => $site->p('page'), 'search' => $site->p('search')));
    }
}
