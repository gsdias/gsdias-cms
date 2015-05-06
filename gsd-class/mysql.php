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

class mySQL implements idatabase {

    protected $conn, $query, $result, $db, $host, $user, $pass, $prepared;
    public $querylist, $total, $errnum, $errmsg, $executed;
    
    protected $_query, $_select, $_from, $_where, $_join, $_on, $_order, $_values;

    public
        // -- Function Name : __construct
        // -- Params : $db,$host,$user,$pass
        // -- Purpose : construct the object and save the params
        function __construct ($db, $host, $user, $pass) {
        $this->db = $db;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->querylist = array();
    }

    protected
        // -- Function Name : connect
        // -- Params :
        // -- Purpose : connects to the database
        function connect ($withdb = true) {
        try {
            ini_set('memory_limit', '512M');

            $db = $withdb ? sprintf('dbname=%s', $this->db) : '';

            $this->conn = new \PDO('mysql:host=' . $this->host . ';charset=utf8;' . $db, $this->user, $this->pass, array(
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                //\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //\PDO::ATTR_PERSISTENT => false
            ));
            $this->conn->exec("SET time_zone = 'Europe/London';");

        }

        catch (\PDOException $error) {
            echo $error->getMessage();
            echo $error->getCode();
            switch ($error->getCode()) {
                case '2002':
                printf('<span style="color: red;">Could not connect to database. Check host</span><br>');
                break;
                case '1044':
                printf('<span style="color: red;">Could not connect to database. Check permissions</span><br>');
                break;
                case '1045':
                printf('<span style="color: red;">Could not connect to database. Check credentials</span><br>');
                break;
                case '1049':
                $this->connect(false);
                break;
                exit;
            }
        }

    }

    protected
        // -- Function Name : formatDates
        // -- Params : $values
        // -- Purpose : Format dates to be in a mysql format validation
        function formatDates ($values) {
        $express = '/^([\d]{1,2})-([\d]{1,2})-([\d]{4})$/';

        foreach ($values as $key => $value) {
            preg_match($express, $value, $matches);
            if (sizeof($matches) === 4) {
                $values[$key] = preg_replace($express, '$3-$2-$1', $value);
            }
        }

        return $values;
    }

    protected
        // -- Function Name : formatOutputDates
        // -- Params : $values
        // -- Purpose : Format dates to be in Europe format reading
        function formatOutputDates ($values) {
        $express = '/^([\d]{4})-([\d]{1,2})-([\d]{1,2})$/';

        foreach ($values as $key => $value) {
            preg_match($express, $value, $matches);

            if (sizeof($matches) === 4) {
                $values[$key] = preg_replace($express, '$3-$2-$1', $value);
            }
        }

        return $values;
    }

    public
        // -- Function Name : statement
        // -- Params : $query,$values = null
        // -- Purpose : save query, prepare statement and calls execute function
        function statement ($query, $values = null) {
        global $tpl;
        $this->query = $query ? $query : $this->query;

        $values = $values ? $this->formatDates($values) : $values;

        if (!$this->conn) {
            $this->connect();
        }
        if ($this->conn) {
            $this->prepared = $this->conn->prepare($query);
            $this->execute($values);
        }

        if (defined('DEBUG') && DEBUG) {

            $tpl->adderror(vsprintf(str_replace('?', '"%s"', $query), $values));

            array_push($this->querylist, $query);
            if ($this->errnum) {
                $tpl->adderror(sprintf("(<strong style='font-weight: 700'>%s</strong>) %s", $this->errnum, $this->errmsg));
            }
        }

    }

    public
        // -- Function Name : execute
        // -- Params : $values = null
        // -- Purpose : executes database query
        function execute ($values = null) {
        global $tpl;
        try {
            $this->executed = $this->prepared->execute($values);
        }

        catch(PDOException $e) {
            echo $e;
        }

        $this->total = $this->prepared->rowCount();
        $erro = $this->prepared->errorInfo();

        $this->result = $this->prepared->fetchAll(PDO::FETCH_OBJ);

        $this->errnum = $erro[1];
        $this->errmsg = $erro[2];

        if (sizeof($this->result) > 0) {
            foreach ($this->result as $key => $values) {
                $this->result[$key] = $this->formatOutputDates($values);
            }
        }

        if ($this->errnum && defined('IS_ADMIN')) {
            $tpl->repvar('SHOW_COOKIE', 'is-visible');
            $tpl->setvar('ALERT_MSG', sprintf('%s: %s', $this->errnum, $this->errmsg));
        }
    }

    public
        // -- Function Name : result
        // -- Params :
        // -- Purpose : returns database query result
        function result() {
        return sizeof($this->result) ? $this->result : array();
    }

    public
        // -- Function Name : singleresult
        // -- Params :
        // -- Purpose : returns database query single result
        function singleresult() {
        return sizeof($this->result) ? $this->result[0] : array();
    }

    public
        // -- Function Name : singleline
        // -- Params :
        // -- Purpose : returns database query single result
        function singleline() {
        return sizeof($this->result) ? $this->result[0] : array();
    }

    public
        // -- Function Name : close
        // -- Params :
        // -- Purpose : closes database connection
        function close () {
        $this->conn = null;
    }

    public
        // -- Function Name : lastInserted
        // -- Params :
        // -- Purpose : returns last inserted id
        function lastInserted () {
        return $this->conn->lastInsertId();
    }
    
    public function reset () {
        $this->_select = '';
        $this->_from = '';
        $this->_join = array();
        $this->_on = array();
        $this->_order = array();
        $this->_where = array();
        $this->_values = array();
        
        return $this;
    }
    
    public function select ($value = '*') {
        $this->_select .= $value;
        
        return $this;
    }
    
    public function from ($value) {
        $this->_from .= $value;
        
        return $this;
    }
    
    public function join ($value, $side = '') {
        $side = strtoupper($side);
        switch ($side) {
            case 'LEFT':
                $value = ' LEFT JOIN ' . $value;
            break;
            case 'RIGHT':
                $value = ' RIGHT JOIN ' . $value;
            break;
            default:
                $value = ' JOIN ' . $value;
        }
        array_push($this->_join, $value);
        
        return $this;
    }
    
    public function on ($value) {
        array_push($this->_on, $value);
        
        return $this;
    }
    
    public function order ($value) {
        array_push($this->_order, $value);
        
        return $this;
    }
    
    public function values ($values = array()) {
        if (is_array($values)) {
            array_merge($this->_values, $values);
        } else {
            array_push($this->_values, $values);
        }
        
        return $this;
    }
    
    public function where ($value) {
        array_push($this->_where, $value);
        
        return $this;
    }
    
    public function exec () {
        $string = '';
        
        $string .= $this->_select ? 'SELECT ' . $this->_select : '';
        $string .= $this->_from ? ' FROM ' . $this->_from : '';
        if (!empty($this->_join) && sizeof($this->_join) === sizeof($this->_on)) {
            foreach ($this->_join as $index => $join) {
                $string .= $join . ' ON ' . $this->_on[$index];
            }
        }
        $string .= !empty($this->_where) ? ' WHERE ' . implode(' ', $this->_where) : '';
        $string .= !empty($this->_order) ? ' ORDER BY ' . implode(' ', $this->_order) : '';
        
        $this->query = $string;
        
        $this->_values = empty($this->_values) ? array() : $this->formatDates($this->_values);

        if (!$this->conn) {
            $this->connect();
        }
        if ($this->conn) {
            $this->prepared = $this->conn->prepare($this->query);
            $this->execute($this->_values);
        }
        
        return $this;
    }

}
