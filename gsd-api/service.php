<?php

require_once('_other' . PHPEXT);
require_once('_' . strtolower($_SERVER['REQUEST_METHOD']) . PHPEXT);

$other = '../resources/api/_other' . PHPEXT;

if (file_exists($other)) {
    require_once($other);
}

$method = '../resources/api/_' . strtolower($_SERVER['REQUEST_METHOD']) . PHPEXT;

if (file_exists($method)) {
    require_once($method);
}
