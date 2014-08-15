<?php

if (@$_REQUEST['save']) {
    
    $content = file_get_contents($_FILES['layout']["tmp_name"]);
    
    preg_match_all(sprintf('#<!-- %s (.*?) -->#s', 'PLACEHOLDER'), $content, $matches, PREG_SET_ORDER);
    $list = array ();
    foreach ($matches as $match) array_push($list, $match[1]);

    while ($key = array_pop($list)) {
        echo $key;
    }
    
    
//    $file = savefile ($_FILES['asset'], TPLPATH . 'layouts/', null, null, $id);
//        
//    $fields = array(
//        $_REQUEST['name'],
//        $_REQUEST['file'],
//        $user->id
//    );
//    
//    $mysql->statement('INSERT INTO layouts (name, file, creator) values(?, ?, ?);', $fields);
//
//    if ($mysql->total) {
//        
//        header("Location: /admin/images", true, 302);
//        exit;
//    }
}
