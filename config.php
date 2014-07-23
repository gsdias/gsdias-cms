<?php

include_once(CLASSPATH . 'application.class' . PHPEXT);
include_once(CLASSPATH . 'email.class' . PHPEXT);
include_once(CLASSPATH . 'image.class' . PHPEXT);
include_once(CLASSPATH . 'menu.class' . PHPEXT);
include_once(CLASSPATH . 'mysql.class' . PHPEXT);
include_once(CLASSPATH . 'notification.class' . PHPEXT);
include_once(CLASSPATH . 'template.class' . PHPEXT);
include_once(CLASSPATH . 'user.class' . PHPEXT);
include_once(CLASSPATH . 'functions' . PHPEXT);

/*
ACCESS LEVEL

ROOT          100
ADMIN_MODIFY   90
ORG            20
AVIAGENS       16
TRADUTOR       15
ADMIN_VIEW     10

*/

date_default_timezone_set('Europe/Lisbon');

$tpl = class_exists('tpl') ? new tpl() : '';

$lang['lang'] = sprintf("%s", $config['lang']);
include_once(COREPATH . 'lang_' . $lang['lang'] . PHPEXT);

$_mysql = array();
$_mysql['host'] = $mysql['host'];
$_mysql['user'] = $mysql['user'];
$_mysql['pass'] = $mysql['pass'];
$_mysql['db'] = $mysql['db'];
define('DBSHARED', $mysql['shared']);

$_db = $_site = $_name = array();

$temp_root = explode('/', $_SERVER['PHP_SELF']);
$root = '';
foreach ($temp_root as $id => $path) {
    if ($id < (sizeof($temp_root)-1)) {
        $root .= $path . '/';
    }
}

$sitemail = $config['email'];
$pathsite = $config['url'];
$resources = $config['resources'];

/* Facebook information */
$fb = sprintf('var appId="%s", page="%s";', $config['facebook']['appid'], $config['facebook']['page']);
$fbconfig = array('appId' => $config['facebook']['appid'], 'secret' => $config['facebook']['secret']);

$_title = $config['title'];
$prefix = $config['prefix'];
$prefix2 = $config['prefix'];

$tpl->setVar('SCRIPT', sprintf('server = "undefined" === typeof server ? { } : server;server.lang = "%s";%s', $lang['lang'], $fb));
$tpl->setVar('TITLE', $config['title']);
$tpl->setVar('DESCRIPTION', $config['description']);
$tpl->setVar('KEYWORDS', $config['keywords']);
$tpl->setVar('PATH', 'http://www.addons.' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '/');
$tpl->setVar('URL', $pathsite . '/');
$tpl->setVar('CDN', CDN);
$tpl->setVar('PATHS', $pathsite . $_SERVER['REQUEST_URI']);
$tpl->setVar('PREFIX', $prefix2);
$tpl->setVar('URL_PRO', $config['pro']);
$tpl->setVar('URL_ICE', $config['ice']);
$tpl->setVar('URL_SVE', $config['sve']);
$tpl->setVar('URL_EVS', $config['evs']);
$tpl->setVar('WEBMASTER', $config['webmaster']);

$mysql = class_exists('mySQL') ? new mySQL($_mysql['db'], $_mysql['host'], $_mysql['user'], $_mysql['pass']) : null;

$string = file_get_contents(XMLPATH . 'divisions.json');
$json_a = json_decode($string, true);

$distritos = array();
$concelhos = array();
$parishes = array();

/* Creates arrays of districts, counties and parishes available */
foreach ($json_a as $elem) {
    $distritos[$elem['CodDist']] = $elem['Distrito'];
    $concelhos[$elem['CodDist']][$elem['CodMun']] = $elem['Municipio'];
    $parishes[$elem['CodDist']][$elem['CodMun']][$elem['UnCodigoFreg']] = $elem['Freguesia'];
}

$string = file_get_contents(XMLPATH . 'themes.json');
$json_a = json_decode($string, true);

foreach ($json_a['themes']['theme'] as $key => $value) {
    $themes[$value['id']] = $value['name'];
}


$string = file_get_contents(XMLPATH . 'countries.json');
$json_a = json_decode($string, true);

$paises = array();
$subsidy = array();
$dial = array();
$nationality = array();
$languages = array();

/* Creates arrays of countries and nationalities available */
foreach ($json_a as $country) {
    $paises[$country['un']] = $country['pais'];
    if (isset($country['dial'])) {
        $dial[$country['un']] = $country['dial'];
    }
    if (isset($country['subsidy'])) {
        $subsidy[$country['un']] = $country['subsidy'];
    }
    if (isset($country['nationality']) && $country['nationality'] != 'null') {
        $nationality[$country['un']] = $country['nationality'];
    }
    if (isset($country['idioma']) && $country['idioma'] != 'null') {
        $languages[$country['un']] = $country['idioma'];
    }
}

asort($paises);
asort($languages);
asort($distritos);
