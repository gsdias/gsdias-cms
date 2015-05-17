<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
//    require ROOTPATH.'gsd-include/php-mo.php';
//    phpmo_convert(ROOTPATH.'gsd-locale/pt_PT/LC_MESSAGES/messages.po', ROOTPATH.'gsd-locale/pt_PT/LC_MESSAGES/messages.mo');
}

$content = file_get_contents(ROOTPATH.'gsd-locale/pt_PT/LC_MESSAGES/messages.po');

preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);

$lang = array();
foreach ($matches as $match) {
    $lang[] = array(
        'FIELD_ID' => new GSD\input(array('label' => 'ID', 'value' => $match[1])),
        'FIELD_STR' => new GSD\input(array('label' => 'Text', 'value' => $match[2]))
    );
}
$tpl->setarray('FIELD', $lang);


$params=array('src'=>"msgid \"TESTE\"\nmsgstr \"teste\"");
$defaults = array(
CURLOPT_URL => 'https://localise.biz/api/convert/po/messages.mo?format=gettext&locale=pt_PT',
CURLOPT_POST => true,
CURLOPT_POSTFIELDS => $params,
);
$ch = curl_init();
curl_setopt_array($ch, ($defaults));
ob_start();
curl_exec($ch);
$message = ob_get_contents();
ob_end_clean();
file_put_contents(ROOTPATH.'gsd-locale/pt_PT/LC_MESSAGES/messages.mo', $message);
curl_close($ch);
