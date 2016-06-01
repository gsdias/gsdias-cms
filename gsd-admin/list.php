<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
$section = $site->arg(1);
$id = $site->arg(2);
$action = $site->arg(3);

$file = CLIENTPATH.'include/admin/fields'.PHPEXT;
if (is_file($file)) {
    include_once $file;
}

if (class_exists('GSD\\Extended\\extended'.$section)) {
    $classsection = 'extended'.$section;
} elseif (class_exists('GSD\\'.$section)) {
    $classsection = $section;
} else {
    $classsection = '';
}

//ACTION DETECTED
if ($action) {
    if ($classsection) {
        $csection = \GSD\sectionFactory::create($classsection);

        $site->main = sprintf('%s/%s', $section, $action);

        if ($action === 'edit') {
            $site->main = '_fields';
        } else if ($action === 'remove') {
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
        $csection = \GSD\sectionFactory::create($classsection);

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

        $csection = \GSD\sectionFactory::create($classsection);

        $csection->getlist(array('numberPerPage' => $numberPerPage, 'page' => @$_REQUEST['page'], 'search' => @$_REQUEST['search']));
    }
}
