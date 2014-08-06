<?php

$section = @$site->path[1];
$id = @$site->path[2];
$action = @$site->path[3];

//ACTION DETECTED
if ($action) {
    include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);
    
    if (class_exists($section)) {
        $sections = new $section ();
        $sections->getcurrent($id);
        
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $action, PHPEXT);

        include_once($file);
        $main = sprintf('%s/%s', $section, $action);
    }

} else {

    //ID DETECTED
    if ($id) {
        $main = sprintf('%s/%s', $section, $id);
        $file = sprintf('gsd-admin/%s/actions/%s%s', $section, $id, PHPEXT);

        if (is_file($file)) {
            include_once($file);
        }
        
    //LISTING
    } else if (@$tables[$section]) {
        $numberPerPage = 10;
        
        $sections = new $section ();
        $sections->getlist($numberPerPage);
    }
}
