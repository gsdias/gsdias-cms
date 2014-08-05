<?php

class tpl {
    var $config = array(
        'vars' => array(), 
        'files' => array(), 
        'error' => array(), 
        'array' => array(), 
        'conditions' => array(), 
        'paths' => array(), 
        'file' => '', 
        'path' => ''
    );
    
    
    /** 
      * @desc Saves the value of a template's variable
      * @param string $id - given id
      * @param string $value - given value
      * @return nothing
    */  
    function setVar ($id, $value = '') {
        $this->config['vars'][$id] = sprintf("%s%s", @$this->config['vars'][$id], $value);
    }
    
    /** 
      * @desc Saves the value of a template's variable
      * @param string $id - given id
      * @param string $value - given value
      * @return nothing
    */  
    function setVars ($values = array()) {
        if (!empty($values)) {
            foreach ($values as $id => $value) {
                $this->setvar($id, $value);
            }
        }
    }
    
    /** 
      * @desc Replaces the value of a saved variable
      * @param string $id - given id
      * @param string $value - given value
      * @return nothing
    */  
    function repVar ($id, $value = '') {
        $this->config['vars'][$id] = $value;
    }
    
    /** 
      * @desc Saves an array of a template's loop
      * @param string $id - given id
      * @param array $value - given list
      * @return nothing
    */  
    function setArray ($id, $value = array(), $merge = false) {
        if ($merge) {
            $this->config['array'][$id] = array_merge($this->config['array'][$id], $value);
        } else {
            $this->config['array'][$id] = $value;
        }
    }
    
    /** 
      * @desc Saves an array of a template's loop
      * @param string $id - given id
      * @param array $value - given list
      * @return nothing
    */  
    function setcondition ($id, $value = true) {
        $this->config['conditions'][$id] = $value;
    }
    
    /** 
      * @desc 
      * @param string $path - 
      * @return nothing
    */  
    function setpaths ($path = array()) {
        $this->config['paths'] = $path;
    }
    
    private
    /** 
      * @desc 
      * @param string $path - 
      * @return nothing
    */  
    function checkblocks ($type = 'BLOCK') {
        $list = array();
        
        $type = strtoupper($type);
                
        preg_match_all(sprintf('#<!-- %s (.*?) -->#s', $type), $this->config['file'], $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) array_push($list, $match[1]);
        
        while ($key = array_pop($list)) {
            preg_match_all(sprintf('#<!-- %s %s -->(.*?)<!-- END%s %s -->#s', $type, $key, $type, $key), $this->config['file'], $matches, PREG_SET_ORDER);
            for ($i = 0; $i < count($matches); $i++) {
                if ($type === 'LOOP') {
                    $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->.*?<!-- END%s %s -->#s', $type, $key, $type, $key), $this->loopBlock($key, $matches[$i][1]), $this->config['file'], 1);
                } else {
                    $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->.*?<!-- END%s %s -->#s', $type, $key, $type, $key), $this->ifBlock($key, $matches[$i][1]), $this->config['file'], 1);
                }
            }
        }
    }
    
    private
    /** 
      * @desc Replaces the variable id for it's value
      * @return nothing
    */  
    function replaceVar () {
        foreach ($this->config['vars'] as $id => $value)  {
            $this->config['file'] = str_replace(sprintf("{%s}", $id), $value, $this->config['file']);
        }
    }

    private 
    /**
      *
    */    
    function loopBlock ($blockid, $block) {
        $param = explode(' ', $blockid);
        $blockid = $param[0];
        $bloco = '';
        $_block = $block;
        if (isset($this->config['array'][$blockid])) {
            $info = $this->config['array'][$blockid];
            $c = 1;
            
            $start = sizeof($param) > 2 ? $param[1] : 1;
            $end = sizeof($param) > 1 ? $param[sizeof($param) - 1] : sizeof($info);
            
            $end = $end == 'END' ? sizeof($info) : $end;
            
            foreach ($info as $_array) {
                if ($c >= $start && $c <= $end) {
                    $_block = $block;
                    foreach ($_array as $_var => $_value) {
                        $_block = str_replace(sprintf("{%s_%s}", $blockid, $_var), $_value, $_block);
                    }
                    $bloco .= sprintf("%s\r\n", $_block);
                }
                $c++;
            }
        }
        return substr($bloco, 0, -2);
    }
    private function ifBlock ($blockid, $block) {
        if (strpos($blockid, " OR ")) {
            $list = explode(" ", $blockid);
            $detected = 0;
            foreach ($list as $blk) {
                if (substr($blk, 0, 1) == "!")  {
                    $detected = $this->config['conditions'][substr($blk, 1)] === false || $detected ? 1 : 0;
                } else {
                    $detected = $this->config['conditions'][$blk] === true || $detected ? 1 : 0;
                }
            }  
            return $detected ? $block : '';
        }
        if (strpos($blockid," AND ")) {
            $list = explode(" ", $blockid);
            $detected = 1;
            foreach ($list as $blk) {
                if ($blk == "AND")
                    continue;
                if (substr($blk, 0, 1) == "!") $detected = $this->config['conditions'][substr($blk, 1)] === false && $detected ? 1 : 0;
                else $detected = $this->config['conditions'][$blk] === true && $detected ? 1 : 0;
            }  
            return $detected ? $block : '';
        }
        
        if (substr($blockid,0,1) == "!") {
            return !@$this->config['conditions'][substr($blockid,1)] || @$this->config['conditions'][substr($blockid,1)] === false ? $block : '';
        } else {
            return @$this->config['conditions'][$blockid] === true ? $block : '';
        }
    }

    private function findFile ($file, $pathname = 0) {
        global $path;
        
        if (isset($this->config['files'][$file])) {
            return $pathname ? '' : file_get_contents($this->config['files'][$file]);
        }
        
        $file = strtolower($file);
        
        foreach($this->config['paths'] as $_path) {
            $cpath = sprintf($_path, $file, $file);
            
            if (file_exists($cpath)) {
                return $pathname ? $cpath : file_get_contents($cpath);
            }
            
            $cpath = sprintf($_path, $path[0], $file);
            
            if (file_exists($cpath)) {
                return $pathname ? $cpath : file_get_contents($cpath);
            }
        }
        
        return '';
    }

    function setFile ($file) {
        global $path;
        
        $file = strtolower($file);
        $this->config['file'] = $this->findFile($file);
        
        $this->sendError();
        
        preg_match_all('*\[[A-Z_]+\]*', $this->config['file'], $matches);
        while(sizeof($matches[0]) > 0) {
            foreach ($matches[0] as $file) {
                $value = str_replace(array('[', ']'), "", $file);
                $files = $this->findFile($value);
                $this->config['file'] = str_replace(sprintf("[%s]", $value), $files, $this->config['file']);
                $this->replaceVar();
                $this->checkblocks('BLOCK');
                $this->checkblocks('IF');
                $this->checkblocks('LOOP');
            }
            preg_match_all('*\[[A-Z_]+\]*', $this->config['file'], $matches);
        }
        $this->replaceVar();
        $this->checkblocks('BLOCK');
        $this->checkblocks('IF');
        $this->checkblocks('LOOP');
        $this->config['file'] = preg_replace('#\{([A-Z0-9_]*)\}#s', '', $this->config['file']);
    }
    
    function includeFiles ($id, $file = null) {

        $idFile = strtolower($file);
        
        if ($file) {
            if ($filefound = $this->findFile($idFile, 1)) {
                $this->config['files'][$id] = $filefound;
            } else if (is_file($file)) {
                $this->config['files'][$id] = $file;
            }
        } else {
            unset($this->config['files'][$id]);
        }
    }
    
    function includeHtml ($html) {
        $this->config['file'] .= $html;
    }
    
    function __toString () {
        global $mysql;
        
        if (!defined('DEBUG') || !DEBUG) {
            $this->config['file'] = str_replace(array("\r\n", "\r", "\n"), array('', '', ''), $this->config['file']);
        }
        
        ob_start();
        
        eval(" ?>" . $this->config['file'] . "<?php ");
        
        $output = ob_get_contents();
        
        ob_end_clean();                
        
        return $output;
        
        if (defined('DEBUG') && DEBUG) {
            $this->setarray('DEBUGLIST', $this->config['error']);
            $constants = get_defined_constants(true);
            foreach ($constants['user'] as $constant => $value) {
                printf('<tr><td>%s: %s</td></tr>', $constant, $value);
            }
        }
    }
    
    function sendError () {
        $this->config['error'] = array_reverse($this->config['error']);
        if (constant('DEBUG') == '1')
            $this->setarray('DEBUGLIST', $this->config['error']);
    }
    
    function addError ($info) {
        array_push($this->config['error'], array('MSG' => $info));
    }
    
    function returnFile ($file) {
        $lines = file_get_contents($this->config['path'] . TPLPATH . $file . TPLEXT);
        $output .= $this->replaceVar ($lines);
        return $output;
    }
}
