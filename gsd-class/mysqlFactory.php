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
namespace GSD;

class mysqlFactory
{
    public static function create($db, $host, $user, $pass)
    {
        return new mySQL($db, $host, $user, $pass);
    }
}
