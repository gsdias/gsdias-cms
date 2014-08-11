<?php

if (@$_REQUEST['save']) {
    
    foreach ($_REQUEST as $name => $value) {
        if (strpos($name, 'gsd-') !== false) {
            $mysql->statement('UPDATE options SET value = ? WHERE name = ?', array($value, $name));
        }
    }
    
    header("Location: /admin", true, 302);
    exit;
}

$mysql->statement('SELECT * FROM options ORDER BY `index`;');

$options = array();
foreach ($mysql->result() as $item) {
    $extraclass = '';
    if (strpos($item['name'], '-image') === false) {
        $field = (string)new input(array('name' => $item['name'], 'value' => @$item['value'], 'label' => $item['label']));
    } else {
        $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($item['value']));
        $image = $mysql->singleline();
        $image = new image(array('path' => sprintf('/gsd-assets/images/%s/%s.%s', @$image['iid'], @$image['iid'], @$image['extension']), 'height' => '100', 'width' => 'auto', 'class' => 'preview'));

        $partial = new tpl();
        $partial->setvars(array(
            'LABEL' => $item['label'],
            'NAME' => $item['name'],
            'IMAGE' => $image
        ));
        $partial->setfile('_image');

        $field = $partial;
        $extraclass = 'image';
    }

    $options[] = array(
        'FIELD' => $field,
        'EXTRACLASS' => $extraclass
    );
}

$tpl->setarray('FIELD', $options);
