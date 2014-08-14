<?php

if (@$_REQUEST['save']) {
    $name = explode('.', $_FILES['asset']['name']);
    $extension = end($name);
        
    $size = getimagesize($_FILES['asset']["tmp_name"]);
    
    $fields = array(
        $_REQUEST['name'],
        $_REQUEST['description'],
        $extension,
        $size[0],
        $size[1],
        round(filesize($_FILES['asset']["tmp_name"]) / 1000, 0) . 'KB',
        $user->id
    );
    
    $mysql->statement('INSERT INTO images (name, description, extension, width, height, size, creator) values(?, ?, ?, ?, ?, ?, ?);', $fields);

    if ($mysql->total) {
        
        $id = $mysql->lastInserted();
        
        $file = savefile ($_FILES['asset'], ASSETPATH . 'images/', null, null, $id);

        header("Location: /admin/images", true, 302);
        exit;
    }
}
