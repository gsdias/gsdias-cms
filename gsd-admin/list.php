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
$currentpage = $site->referer('page') ? '?page='.$site->referer('page') : '';

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

        $csection->getcurrent($id);
        
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
            $tpl->setvar('CURRENT_PAGE', $currentpage);
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
        $numberPerPage = $site->options['numberPerPage']->value;

        $csection = \GSD\sectionFactory::create($section);

        $csection->getlist(array('numberPerPage' => $numberPerPage, 'page' => $site->p('page'), 'search' => $site->p('search')));
    }
}
