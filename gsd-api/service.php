<?php

require_once('_other' . PHPEXT);
require_once('_' . strtolower($_SERVER['REQUEST_METHOD']) . PHPEXT);

$other = CLIENTPATH . 'api/_other' . PHPEXT;

if (is_file($other)) {
    require_once($other);
}

$method = CLIENTPATH . 'api/_' . strtolower($_SERVER['REQUEST_METHOD']) . PHPEXT;

if (is_file($method)) {
    require_once($method);
}
