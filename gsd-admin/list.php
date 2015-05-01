<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

$section = $site->arg(1);
$id = $site->arg(2);
$action = $site->arg(3);

$file = CLIENTPATH . 'include/admin/fields' . PHPEXT;
if (is_file($file)) {
    include_once($file);
}

//ACTION DETECTED
if ($action) {
    
    if (class_exists($section) || class_exists('client' . $section)) {

        if (class_exists('client' . $section)) {
            $classsection = 'client' . $section;
        } else {
            $classsection = $section;
        }

        $csection = new $classsection ();

        $site->main = sprintf('%s/%s', $section, $action);
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $action, PHPEXT);
        if (is_file($file)) {
            include_once($file);
        }
        
        $file = sprintf('%sinclude/admin/%s/actions/%s%s', CLIENTPATH, $section, $action, PHPEXT);

        if (is_file($file)) {
            include_once($file);
        }

        $current = $csection->getcurrent($id);

        if ($action === 'edit') {
            $csection->generatefields($section, $current);
        }
    }

} else {

    //ID DETECTED
    if ($id) {
        
        if (class_exists('client' . $section)) {
            $classsection = 'client' . $section;
        } else {
            $classsection = $section;
        }

        $csection = new $classsection ();
        
        $csection->generatefields($section, null);
        
        $site->main = sprintf('%s/%s', $section, $id);
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $id, PHPEXT);

        if (is_file($file)) {
            include_once($file);
        }
        
        $file = sprintf('%sinclude/admin/%s/actions/%s%s', CLIENTPATH, $section, $id, PHPEXT);

        if (is_file($file)) {
            include_once($file);
        }

    //LISTING
    } else {
        $numberPerPage = 10;
        
        if (class_exists('client' . $section)) {
            $classsection = 'client' . $section;
        } else {
            $classsection = $section;
        }
        
        $csection = new $classsection ();

        $csection->getlist($numberPerPage);
    }
}
