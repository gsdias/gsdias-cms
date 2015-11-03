<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
    $fields = 1;

    $mysql->reset()
        ->select()
        ->from('options')
        ->order('index')
        ->exec();

    $options = array();
    foreach ($mysql->result() as $item) {
        $value = @$_REQUEST[$item->name];
        $name = $item->name;
        if (strpos($item->name, '_checkbox') !== false) {
            $value = @$_REQUEST[$item->name] ? @$_REQUEST[$item->name] : null;
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

$mysql->reset()
    ->select()
    ->from('options')
    ->order('index')
    ->exec();

$options = array();
foreach ($mysql->result() as $item) {
    $extraclass = '';
    if (strpos($item->name, '_image') !== false) {
        $image = new GSD\image(array(
            'iid' => $item->value,
            'height' => '100',
            'width' => 'auto',
            'class' => sprintf('preview %s', $item->value ? '' : 'is-hidden'),
        ));

        $partial = new GSD\tpl();
        $partial->setvars(array(
            'LABEL' => $item->label,
            'NAME' => $item->name,
            'IMAGE' => $image,
            'VALUE' => @$item->value ? @$item->value : 0,
            'EMPTY' => @$item->value ? 'is-hidden' : '',
        ));
        $partial->setfile('_image');

        $field = $partial;
        $extraclass = ' image';
    } elseif ($item->name === 'gsd-locale_select') {
        $field = new GSD\select(array(
            'id' => $item->name,
            'name' => $item->name,
            'list' => $languages,
            'label' => $item->label,
            'selected' => @$item->value,
        ));
    } elseif (strpos($item->name, '_checkbox') !== false) {
        $extraclass = ' checkbox';
        $field = new GSD\input(array(
            'id' => $item->name,
            'name' => $item->name,
            'label' => $item->label,
            'selected' => @$item->value,
            'value' => 1,
            'type' => 'checkbox',
        ));
    } else {
        $field = (string) new GSD\input(array('name' => $item->name, 'value' => @$item->value, 'label' => $item->label));
    }

    $options[] = array(
        'FIELD' => $field,
        'EXTRACLASS' => $extraclass,
    );
}

$tpl->setarray('FIELD', $options);
