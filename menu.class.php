<?php
/*************************************
	* File with vaga class information  *
	*************************************/

class menu {
    public $ice, $sve, $proatlantico;
    public function __construct ($prefix) {
        global $mysql;
        $this->ice = $this->sve = $this->proatlantico = array();
    }
    
    public function savemenu ($prefix) {
        global $mysql;
        $mysql->statement(sprintf('SELECT SQL_CACHE * FROM %s.menu ORDER BY weight;', $prefix));
        $this->{$prefix}['menu'] = $mysql->result();
        $this->generatefooter($prefix);
    }
    
    public function generatefooter ($prefix) {
        global $path, $config;
        
        $page = $path[0]
        
        $list = '';
        
        foreach ($this->{$prefix}['menu'] as $menu) {
            
            $url = $config['lang'] == 'en' ? $menu['url_en']: $menu['url'];
            $name = $config['lang'] == 'en' ? $menu['name_en'] : $menu['name'];
            if ($menu['parent_id'] == '') {
                $list .= sprintf('<dd><a href="%s%s">%s</a></dd>', $config[$prefix], $url, $name);
            }
        }
        
        $this->{$prefix}['tpl'] = $list;
    }
    
    public function removePages ($uri) {
        foreach($uri as $key => $param) {
            if (is_numeric($param)) {
                unset($uri[$key]);
            } 
        }
        return $uri;
    }
    
    public function getid ($prefix, $url) {
        global $_SERVER;
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $uri = implode('/', $this->removePages($uri));
        foreach ($this->{$prefix}['menu'] as $menu) {
            if ($uri == $menu['url'] && $menu['parent_id'] != '') {
                return $menu['parent_id'];
            }
        }
        return;
    }
}
