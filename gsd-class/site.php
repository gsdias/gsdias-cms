<?php
/*************************************
* File with user class information  *
*************************************/

class site {
    
    public $name, $email, $ga;
    
    public function __construct () {
        global $mysql;
        
        $mysql->statement('SELECT * FROM options;');
        
        foreach ($mysql->result() as $option) {
            $this->{str_replace('gsd-', '', $option['name'])} = $option['value'];
        }
    }
    
}
