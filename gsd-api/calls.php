<?php

class api {

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

    public $user;

    public
    // -- Function Name : __construct
    function __construct () {
        $this->output = array('error' => 0, 'message' => '');
        $this->user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        $this->loginRequired = array(
            'vacancy',
            'reference',
            'users'
        );
        $this->adminRequired = array(
            ''
        );
    }


    public function method ($type, $cmd, $extra = null, $fields = null, $doc = false, $commands) {

        $method = $type . $cmd;

        if (($this->checkCredentials($cmd) && $this->checkPermission($method, $cmd)) || $doc) {

            if (isset($commands[$method])) {
                global $$method;
                $this->output = $$method($fields, $extra, $doc);

            } else {

                $this->output = array('error' => -1, 'message' => "I don't recognize that command");

            }

        } else {

            $this->output = array('error' => -2, 'message' => "You don't have the right permission");

        }
    }

    public function output ($output = null) {

        global $mysql;
        $output = $output === null ? $this->output : $output;
        $output = json_encode($output, true);
        header("Content-length: " . strlen($output)); // tells file size
        header('Content-type: application/json; charset=utf-8');
        echo isset($_GET['jsoncallback']) ? "{$_GET['jsoncallback']}($output)" : $output;

        $mysql->close();
        exit;

    }

    private function fields ($fields = null) {

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

    private function checkCredentials ($cmd) {

        if (in_array($cmd, $this->loginRequired)) {

            if ($this->user != null) {

                return true;

            } else {

                return false;

            }
        }

        return true;
    }

    private function checkPermission ($method, $cmd) {

        if (in_array($method . $cmd, $this->adminRequired)) {

            if ($this->user != null && $this->level >= 90) {

                return true;

            } else {

                return false;

            }
        }

        return true;
    }

    public function requiredFields ($fields, $required) {

        foreach ($required as $field) {

            if (!isset($fields[$field])) {

                return false;

            }

        }

        return true;
    }
}