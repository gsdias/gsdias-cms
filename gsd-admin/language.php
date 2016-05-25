<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
    if ($site->arg(2) === 'extended') {
        $file = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.mo';
    } else {
        $file = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.mo';
    }
    $string = '';
    foreach ($_REQUEST['msgid'] as $i => $value) {
        $id = $value;
        $str = $_REQUEST['msgstr'][$i];
        $string .= "\nmsgid \"$id\"\nmsgstr \"$str\"\n";
    }

    $params = array('src' => $string);
    $defaults = array(
    CURLOPT_URL => 'https://localise.biz/api/convert/po/messages.mo?format=gettext&locale='.$language,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $params,
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($defaults));
    ob_start();
    $converted = curl_exec($ch);
    $message = ob_get_contents();
    ob_end_clean();
    file_put_contents($file, $message);
    curl_close($ch);

    $newfile = "msgid \"\"\nmsgstr \"\"\n\"Project-Id-Version: \\n\"\n\"POT-Creation-Date: 2014-11-12 17:19-0000\\n\"\n\"PO-Revision-Date: 2015-05-15 08:36-0000\\n\"\n\"Last-Translator: \\n\"\n\"Language-Team: \\n\"\n\"MIME-Version: 1.0\\n\"\n\"Content-Type: text/plain; charset=UTF-8\\n\"\n\"Content-Transfer-Encoding: 8bit\\n\"\n\"Plural-Forms: nplurals=2; plural=(n != 1);\\n\"\n\"Language: $language\\n\"\n".$string;

    if ($site->arg(2) === 'extended') {
        $file = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.po';
    } else {
        $file = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.po';
    }

    $content = file_put_contents($file, $newfile);

    if ($content) {
        $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_LANGUAGE_SAVED')));
    } else {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LANGUAGE_NOT_SAVED')));
        $tpl->setcondition('ERRORS');
    }

    if ($converted) {
        $tpl->setarray('MESSAGES', array('MSG' => lang('LANG_LANGUAGE_CONVERTED')));
    } else {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_LANGUAGE_NOT_CONVERTED')));
        $tpl->setcondition('ERRORS');
    }

    if (!empty($tpl->config['array']['MESSAGES'])) {
        redirect('/admin');
    }
}

if ($site->arg(2) === 'extended') {
    $file = CLIENTPATH.'locale/'.$language.'/LC_MESSAGES/extended.po';
} else {
    $file = ROOTPATH.'gsd-locale/'.$language.'/LC_MESSAGES/messages.po';
}

$content = file_get_contents($file);

preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

$lang = array();
foreach ($matches as $match) {
    if ($match[1]) {
        $lang[] = array(
            'FIELD_ID' => new GSD\input(array('label' => 'ID', 'name' => 'msgid[]', 'value' => $match[1])),
            'FIELD_STR' => new GSD\input(array('label' => lang('LANG_TEXT'), 'name' => 'msgstr[]', 'value' => $match[2], 'labelClass' => 'string')),
        );
    }
}
$tpl->setarray('FIELD', $lang);
