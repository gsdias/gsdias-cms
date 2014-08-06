<?php

if (@$_REQUEST['save']) {
    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);
        
    $size = getimagesize($_FILES['asset']["tmp_name"]);
    
    $fields = array(
        $_REQUEST['name'],
        $extension,
        $size[0],
        $size[1],
        $user->id
    );
    
    $mysql->statement('INSERT INTO images (name, extension, width, height, creator) values(?, ?, ?, ?, ?);', $fields);
echo $mysql->errmsg;
    if ($mysql->total) {
        
        $id = $mysql->lastInserted();
        
        $file = savefile ($_FILES['asset'], ASSETPATH . '/images/' . $id . '/', $type = null, $nottype = null, $id);
        
        header("Location: /admin/images", true, 302);
    }
}
