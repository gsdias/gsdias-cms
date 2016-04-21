<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD\Api;

use GSD;

class api
{
    /*

        ERROR: {
            0   :   No error
            -1  :   wrong command
            -2  :   no permission
            -3  :   missing required fields
            -4  :   generic error
        }

    */

    private $loginRequired, $output;

    public $user, $method, $extended;

    // -- Function Name : __construct
    public function __construct($method, $extended)
    {
        $this->output = array('error' => 0, 'message' => '');

        $this->method = $method;
        $this->extended = $extended;
        $this->loginRequired = array();
    }

    public function method($type, $cmd, $extra = null, $fields = null, $doc = false)
    {
        global $_extra;

        $method = $type.$cmd;

        if (method_exists($this->method, $cmd)) {
            http_response_code(200);
            $this->output = $this->method->{$cmd}($fields, $extra, $doc);
        } else {
            $this->output = array('error' => -1, 'message' => "I don't recognize that command");
        }
    }

    public function output($output = null)
    {
        global $mysql;
        $output = $output === null ? $this->output : $output;
        $output = json_encode($output, true);
        header('Content-length: '.strlen($output)); // tells file size
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Origin: ".@$_SERVER['HTTP_ORIGIN']);
        echo isset($_GET['jsoncallback']) ? "{$_GET['jsoncallback']}($output)" : $output;

        $mysql->close();
        exit;
    }

    private function fields($fields = null)
    {
        $output = array();
        $fields = explode('&', $fields);

        foreach ($fields as $field) {
            if ($field) {
                $field = explode('=', $field);
                $output[$field[0]] = $field[1];
            }
        }

        return $output;
    }

    public function checkCredentials($uid = 0)
    {
        global $permissions;

        $this->user = @$_SESSION['user'] ? $_SESSION['user'] : (class_exists('\\GSD\\Extended\\extendeduser') ? new GSD\Extended\extendeduser($uid) : new GSD\user($uid));

        foreach($permissions as $permission) {
            define('IS_'.strtoupper($permission), $permission == $this->user->level);
        }

        return $this->user;
    }

    public function requiredFields($fields, $required)
    {
        foreach ($required as $field) {
            if (!isset($fields[$field])) {
                return false;
            }
        }

        return true;
    }
}
