<?php

$section = @$site->path[1];
$id = @$site->path[2];
$action = @$site->path[3];

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
        
        $csection->getcurrent($id);
        
        $csection->generatefields($id);
        
        $main = sprintf('%s/%s', $section, $action);
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $action, PHPEXT);
        if (is_file($file)) {
            include_once($file);
        }
        
        $file = sprintf('%sinclude/admin/%s/actions/%s%s', CLIENTPATH, $section, $action, PHPEXT);

        if (is_file($file)) {
            include_once($file);
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
        
        $csection->generatefields($id);
        
        $main = sprintf('%s/%s', $section, $id);
        
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
