<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

/**************
 * MENU CLASS *
 **************/

class menu {
    private $menu, $tpl;
    
    public function __construct () {
    }
    
    public function savemenu () {
        global $mysql;
        
        $mysql->statement(sprintf('SELECT SQL_CACHE * FROM menu ORDER BY weight;'));
        $this->menu = $mysql->result();
        $this->generatefooter();
    }
    
    public function generatefooter () {
        global $path, $config;
        
        $page = $path[0];
        
        $list = '';
        
        foreach ($this->menu as $menu) {
            
            $url = $config['lang'] == 'en' ? $menu['url_en']: $menu['url'];
            $name = $config['lang'] == 'en' ? $menu['name_en'] : $menu['name'];
            if ($menu['parent_id'] == '') {
                $list .= sprintf('<dd>%s</dd>', new anchor(array('href' => $url, 'text' => $name)));
            }
        }
        
        $this->tpl = $list;
    }
    
    public function removePages ($uri) {
        foreach($uri as $key => $param) {
            if (is_numeric($param)) {
                unset($uri[$key]);
            } 
        }
        return $uri;
    }
    
    public function getid ($url) {
        global $_SERVER;
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $uri = implode('/', $this->removePages($uri));
        foreach ($this->menu as $menu) {
            if ($uri == $menu['url'] && $menu['parent_id'] != '') {
                return $menu['parent_id'];
            }
        }
        return;
    }
}
