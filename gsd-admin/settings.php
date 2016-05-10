<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $fields = 1;

    $options = array();
    foreach ($site->options as $name => $info) {
        $value = $_REQUEST[$name];

        if ($name === 'version') {
            continue;
        }

        if ($info['type'] === 'checkbox') {
            $value = $value ? $value : null;
        }

        $mysql->reset()
            ->update('options')
            ->fields(array('value'))
            ->where('name = ?')
            ->values(array(escapeText($value), $name))
            ->exec();

        $fields = $mysql->errnum ? 0 : $fields;
    }

    if ($fields == 1) {
        $_SESSION['message'] = lang('LANG_SETTINGS_SAVED');
        header('Location: /admin', true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', lang('LANG_SETTINGS_ERROR'));
        $tpl->setcondition('ERRORS');
    }
}

$options = array();
foreach ($site->options as $name => $info) {
    $extraclass = '';
    $label = $info['label'];
    $value = $info['value'];

    if ($info['type'] === 'image') {
        $image = new GSD\image(array(
            'iid' => $value,
            'height' => '100',
            'width' => 'auto',
            'class' => sprintf('preview %s', $value ? '' : 'is-hidden'),
        ));

        $partial = new GSD\tpl();
        $partial->setvars(array(
            'LABEL' => $label,
            'NAME' => $name,
            'IMAGE' => $image,
            'VALUE' => @$value ? @$value : 0,
            'EMPTY' => @$value ? 'is-hidden' : '',
        ));
        $partial->setfile('_image');

        $field = $partial;
        $extraclass = ' image';
    } elseif ($name === 'locale') {
        $field = new GSD\select(array(
            'id' => $name,
            'name' => $name,
            'list' => $languages,
            'label' => $label,
            'selected' => @$value,
        ));
    } elseif ($info['type'] === 'checkbox') {
        $extraclass = ' checkbox';
        $field = new GSD\input(array(
            'id' => $name,
            'name' => $name,
            'label' => $label,
            'selected' => @$value,
            'value' => 1,
            'type' => 'checkbox',
        ));
    } elseif ($name === 'version') {
        continue;
    } else {
        $field = (string) new GSD\input(array('name' => $name, 'value' => @$value, 'label' => $label));
    }

    $options[] = array(
        'FIELD' => $field,
        'EXTRACLASS' => $extraclass,
    );
}

$tpl->setarray('FIELD', $options);
