<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;

if ($site->p('save')) {
    
    $result = saveKeys();

    $content = $result;
    
    $result = saveKeys(true);

    $content = $result;
    
    if ($content) {
        $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_LANGUAGE_SAVED')));
    } else {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LANGUAGE_NOT_SAVED')));
        $tpl->setcondition('ERRORS');
    }

    if (!empty($tpl->config['array']['MESSAGES'])) {
        redirect('/admin');
    }
}

$lang = getKeys();
$lang = array_merge($lang, getKeys(true));
$tpl->setarray('FIELD', $lang);

function saveKeys($extended = false) {
    global $language, $site;

    if ($extended) {
        $key = 'keyclient';
        $msg = 'valueclient';
    } else {
        $key = 'key';
        $msg = 'value';
    }

    if (!$site->p($key)) {
        return true;
    }

    $stringini = sprintf('[%s]', $language);
    
    foreach ($site->p($key) as $i => $value) {
        $id = $value;
        $str = $site->p($msg)[$i];
        $stringini .= sprintf("\n%s = \"%s\"", $id, $str);
    }

    if ($extended) {
        $fileini = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.ini';
    } else {
        $fileini = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.ini';
    }

    $contentini = file_put_contents($fileini, $stringini);

    return $contentini;
}

function getKeys($extended = false) {
    global $language;
    
    if ($extended) {
        $keys = parse_ini_file(CLIENTPATH."locale/".$language."/LC_MESSAGES/extended.ini");
        $_key = 'keyclient[]';
        $_string = 'valueclient[]';
    } else {
        $keys = parse_ini_file(ROOTPATH."gsd-locale/".$language."/LC_MESSAGES/messages.ini");
        $_key = 'key[]';
        $_string = 'value[]';
    }

    $lang = array();
    foreach ($keys as $key => $value) {
        $lang[] = array(
            'FIELD_ID' => new GSD\input(array('label' => 'Key', 'name' => $_key, 'value' => $key)),
            'FIELD_STR' => new GSD\input(array('label' => lang('LANG_TEXT'), 'name' => $_string, 'value' => $value, 'labelClass' => 'pt-4')),
        );
    }
    return $lang;
}