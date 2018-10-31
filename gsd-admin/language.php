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
    
    $result = generateGettext();

    $content = $result;
    
    $result = generateGettext(true);

    $content = $result;
    
    if ($content) {
        $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_LANGUAGE_SAVED')));
    } else {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LANGUAGE_NOT_SAVED')));
        $tpl->setcondition('ERRORS');
    }

    // if ($converted) {
    //     $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_LANGUAGE_CONVERTED')));
    // } else {
    //     $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LANGUAGE_NOT_CONVERTED')));
    //     $tpl->setcondition('ERRORS');
    // }

    if (!empty($tpl->config['array']['MESSAGES'])) {
        redirect('/admin');
    }
}

$lang = getKeys();
$lang = array_merge($lang, getKeys(true));
$tpl->setarray('FIELD', $lang);

function generateGettext($extended = false) {
    global $language, $site;

    if ($extended) {
        $key = 'msgidclient';
        $msg = 'msgstrclient';
    } else {
        $key = 'msgid';
        $msg = 'msgstr';
    }

    if (!$site->p($key)) {
        return true;
    }

    $string = '';
    $stringini = sprintf('[%s]', $language);
    
    foreach ($site->p($key) as $i => $value) {
        $id = $value;
        $str = $site->p($msg)[$i];
        $string .= "\nmsgid \"$id\"\nmsgstr \"$str\"\n";
        $stringini .= sprintf("\n%s = \"%s\"", $id, $str);
    }

    $newfile = "msgid \"\"\nmsgstr \"\"\n\"Project-Id-Version: \\n\"\n\"POT-Creation-Date: 2014-11-12 17:19-0000\\n\"\n\"PO-Revision-Date: 2015-05-15 08:36-0000\\n\"\n\"Last-Translator: \\n\"\n\"Language-Team: \\n\"\n\"MIME-Version: 1.0\\n\"\n\"Content-Type: text/plain; charset=UTF-8\\n\"\n\"Content-Transfer-Encoding: 8bit\\n\"\n\"Plural-Forms: nplurals=2; plural=(n != 1);\\n\"\n\"Language: $language\\n\"\n".$string;

    if ($extended) {
        $file = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.po';
        $fileini = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.ini';
    } else {
        $file = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.po';
        $fileini = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.ini';
    }

    $contentini = file_put_contents($fileini, $stringini);

    $content = file_put_contents($file, $newfile);

    return $content && $contentini;
}

function getKeys($extended = false) {
    global $language;
    
    if ($extended) {
        $file = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.po';
        $key = 'msgidclient[]';
        $string = 'msgstrclient[]';
    } else {
        $file = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.po';
        $key = 'msgid[]';
        $string = 'msgstr[]';
    }
    $content = file_get_contents($file);

    preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

    $lang = array();
    foreach ($matches as $match) {
        if ($match[1]) {
            $lang[] = array(
                'FIELD_ID' => new GSD\input(array('label' => 'Key', 'name' => $key, 'value' => $match[1])),
                'FIELD_STR' => new GSD\input(array('label' => lang('LANG_TEXT'), 'name' => $string, 'value' => $match[2], 'labelClass' => 'pt-4')),
            );
        }
    }
    return $lang;
}