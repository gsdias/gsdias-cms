<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;
defined('GVALID') or die;

class mysqlFactory
{
    public static function create($db, $host, $user, $pass)
    {
        $pattern = '/(\?)(.*)/';
        $uri = explode('/', preg_replace($pattern, '', $_SERVER['REQUEST_URI']));

        $db = new mySQL($db, $host, $user, $pass);

        if (!defined('IS_INSTALLED')) {
            if ($uri[1] === 'admin' && @$_REQUEST['install'] === 'user') {
                define('IS_INSTALLED', 0);
            } else {
                define('IS_INSTALLED', 1);
            }
        }

        return $db;
    }
}
