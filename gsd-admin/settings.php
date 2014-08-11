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
    $options[] = array(
        'INPUT' => new input(array(
            'name' => $item['name'],
            'label' => $item['label'],
            'value' => $item['value'])
        )
    );
}

$tpl->setarray('SETTINGS', $options);
