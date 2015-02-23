<?php

if (@$_REQUEST['save']) {
    
    $fields = 0;
    foreach ($_REQUEST as $name => $value) {
        if (strpos($name, 'gsd-') !== false) {
            $fields++;
            $mysql->statement('UPDATE options SET value = ? WHERE name = ?', array($value, $name));
            $fields += !$mysql->errnum ? -1 : 0;
        }
    }
    
    if ($fields == 0) {
        $_SESSION['message'] = 'Definições salvas.';
        header("Location: /admin", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', '{LANG_SETTINGS_ERROR}');
        $tpl->setcondition('ERRORS');
    }
}

$mysql->statement('SELECT * FROM options ORDER BY `index`;');

$options = array();
foreach ($mysql->result() as $item) {
    $extraclass = '';
    if (strpos($item['name'], '_image') === false) {
        $field = (string)new input(array('name' => $item['name'], 'value' => @$item['value'], 'label' => $item['label']));
    } else {
        if ($item['value']) {
            $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($item['value']));
            $image = $mysql->singleline();

            $image = new image(array(
                'iid' => $image['iid'],
                'height' => '100',
                'width' => 'auto',
                'class' => sprintf('preview %s', $item['value'] ? '' : 'is-hidden')
            ));
        }

        $partial = new tpl();
        $partial->setvars(array(
            'LABEL' => $item['label'],
            'NAME' => $item['name'],
            'IMAGE' => $image,
            'VALUE' => @$item['value'] ? @$item['value'] : 0,
            'EMPTY' => @$item['value'] ? 'is-hidden' : ''
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
