<?php

include_once('config.php');

if (file_exists('install' . PHPEXT)) {
    require_once('install.php');
}