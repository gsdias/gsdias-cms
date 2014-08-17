<?php

if (@$_REQUEST['save']) {
    
    $content = file_get_contents($_FILES['layout']["tmp_name"]);

//    $file = savefile ($_FILES['asset'], TPLPATH . 'layouts/', null, null, $id);
//        

    $fields = array(
        $_REQUEST['name'],
        $_FILES['layout']["name"],
        $_REQUEST['ltid'],
        $user->id
    );

    $mysql->statement('INSERT INTO layouts (name, file, ltid, creator) values(?, ?, ?, ?);', $fields);
    $lid = $mysql->lastinserted();

    if ($lid) {
        preg_match_all(sprintf('#<!-- %s (.*?) -->#s', 'PLACEHOLDER'), $content, $matches, PREG_SET_ORDER);
        $list = array ();
        foreach ($matches as $match) array_push($list, $match[1]);

        while ($key = array_pop($list)) {
            $sectionname = explode(' ', $key);
            $mysql->statement('INSERT INTO layoutsections (lid, name, creator) values(?, ?, ?);', array(
                $lid,
                $sectionname[0],
                $user->id
            ));
            $lsid = $mysql->lastinserted();
            $mysql->statement('SELECT mtid FROM moduletypes WHERE name = ?', array( strtolower( $sectionname[1] ) ));
            $mtid = $mysql->singleresult();
            $mysql->statement('INSERT INTO layoutsectionmoduletypes (lsid, mtid) values(?, ?);', array(
                $lsid,
                $mtid
            ));
        }
    } else {

            $tpl->setvar('ERRORS', 'Já existe um layout associado a esse ficheiro');
            $tpl->setcondition('ERRORS');

    }
//
//    if ($mysql->total) {
//        
//        header("Location: /admin/images", true, 302);
//        exit;
//    }
}

$mysql->statement('SELECT * FROM layouttypes');

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item['ltid']] = $item['name'];
}

$types = new select( array ( 'list' => $types, 'id' => 'LAYOUTTYPE' ) );
$types->object();
