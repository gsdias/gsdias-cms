<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

    /*************************************
	* File with mySQL class information *
	*************************************/

class mySQLFactory extends mySQL {
    static function create ($db, $host, $user, $pass) {
        return new mySQL($db, $host, $user, $pass);
    }
}