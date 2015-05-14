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
define('ROOTPATH', dirname(__FILE__).'/../');

include_once ROOTPATH.'gsd-include/gsd-config.php';

require_once 'service.php';
require_once 'api.php';

@session_start();

if (!isset($_SERVER['REDIRECT_URL'])) {
    echo 'Missing command';
    exit;
}

$path = explode('/', $_SERVER['REDIRECT_URL']);

$cmdposition = array_search('gsd-api', $path) + 1;

$cmd = $path[$cmdposition];

$cmdposition++;

$extra = array();

while (isset($path[$cmdposition])) {
    if ($path[$cmdposition]) {
        array_push($extra, $path[$cmdposition]);
    }
    $cmdposition++;
}

$doc = sizeof($extra) > 0 && $extra[sizeof($extra) - 1] === 'doc' ? true : false;

if ($doc) {
    array_pop($extra);
}

$input = file_get_contents('php://input');

$json = $input !== null ? json_decode($input, true) : '';

parse_str($input, $post_vars);

$fields = sizeof($post_vars) ? $post_vars : $_REQUEST;

$api = new \GSD\Api\api($_extra['method'], $_extra['other']);

$fields['json'] = $json;

$api->method($_SERVER['REQUEST_METHOD'], $cmd, $extra, $fields, $doc);

$api->output();
