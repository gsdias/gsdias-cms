<?php

class tpl {
    var $config = array(
        'vars' => array(), 
        'files' => array(), 
        'error' => array(), 
        'array' => array(), 
        'file' => '', 
        'path' => ''
    );
    
    
    /** 
      * @desc Saves the value of a template's variable
      * @param string $id - given id
      * @param string $value - given value
      * @return nothing
    */  
    function setVar ($id, $value) {
        $this->config['vars'][$id] = isset($this->config['vars'][$id]) ? sprintf("%s%s", $this->config['vars'][$id], $value) : $value;
        if (constant('DEBUG') == '1') {
            //$this->adderror(sprintf('Added template key <strong>%s</strong>: %s', $id, $value));
        }
    }
    
    /** 
      * @desc Replaces the value of a saved variable
      * @param string $id - given id
      * @param string $value - given value
      * @return nothing
    */  
    function repVar ($id, $value) {
        $this->config['vars'][$id] = $value;
    }
    
    /** 
      * @desc Saves an array of a template's loop
      * @param string $id - given id
      * @param array $value - given list
      * @return nothing
    */  
    function setArray ($id, $value) {
        $this->config['array'][$id] = $value;
    }
    
    /** 
      * @desc 
      * @param string $path - 
      * @return nothing
    */  
    function path ($path) {
        $this->config['path'] = $path;
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
                if (substr($blk,0,1) == "!")  {
                    $detected = !defined(substr($blk, 1)) || $detected ? 1 : 0;
                } else {
                    $detected = defined($blk) || $detected ? 1 : 0;
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
                if (substr($blk, 0, 1) == "!") $detected = !defined(substr($blk, 1)) && $detected ? 1 : 0;
                else $detected = defined($blk) && $detected ? 1 : 0;
            }  
            return $detected ? $block : '';
        }
        
        if (substr($blockid,0,1) == "!") {
            return !defined(substr($blockid,1)) ? $block : '';
        } else {
            return defined($blockid) ? $block : '';
        }
    }

    private function findFile ($file, $pathname = 0) {
        global $path;

        $onarray = @$this->config['files'][$file];
        $first = strtolower(TPLPATH . $file . TPLEXT);
        $sub = strtolower(TPLPATH . $file . '/' . $file . TPLEXT);
        $level = strtolower(TPLPATH . $path[0] . '/' . $file . TPLEXT);
        $shared = strtolower(TPLPATH . '_shared/' . $file . TPLEXT);
        $core = strtolower(COREPATH . 'tpl/' . $file . TPLEXT);
        $subcore = strtolower(COREPATH . 'tpl/' . $file . '/' . $file . TPLEXT);
        $levelcore = strtolower(COREPATH . 'tpl/' . $path[0] . '/' . $file . TPLEXT);
        $editable = strtolower(TPLPATH . '_editable/' . $file . TPLEXT);
        $subeditable = strtolower(TPLPATH . '_editable/' . $file . '/' . $file . TPLEXT);
        $leveleditable = strtolower(TPLPATH . '_editable/' . $path[0] . '/' . $file . TPLEXT);


        if (file_exists($editable)) {
            array_push($this->config['error'], sprintf('Foi adicionado o shared: %s', $editable));
            return $pathname ? $editable : file_get_contents($editable);
        }
        if (file_exists($subeditable)) {
            array_push($this->config['error'], sprintf('Foi adicionado o shared: %s', $subeditable));
            return $pathname ? $subeditable : file_get_contents($subeditable);
        }
        if (file_exists($leveleditable)) {
            array_push($this->config['error'], sprintf('Foi adicionado o shared: %s', $leveleditable));
            return $pathname ? $leveleditable : file_get_contents($leveleditable);
        }
        if (file_exists($shared)) {
            array_push($this->config['error'], sprintf('Foi adicionado o shared: %s', $shared));
            return $pathname ? $shared : file_get_contents($shared);
        }
        if (file_exists($core)) {
            array_push($this->config['error'], sprintf('Foi adicionado o core: %s', $core));
            return $pathname ? $core : file_get_contents($core);
        }
        if (file_exists($subcore)) {
            array_push($this->config['error'], sprintf('Foi adicionado o sub core: %s', $subcore));
            return $pathname ? $subcore : file_get_contents($subcore);
        }
        if (file_exists($levelcore)) {
            array_push($this->config['error'], sprintf('Foi adicionado o level core: %s', $levelcore));
            return $pathname ? $levelcore : file_get_contents($levelcore);
        }
        if (file_exists($onarray)) {
            array_push($this->config['error'], sprintf('Foi adicionado o array: %s', $onarray));
            return $pathname ? $onarray : file_get_contents($onarray);
        }
        if (file_exists($first)) {
            array_push($this->config['error'], sprintf('Foi adicionado o primeiro: %s', $first));
            return $pathname ? $first : file_get_contents($first);
        }
        if (file_exists($sub)) {
            array_push($this->config['error'], sprintf('Foi adicionado o sub: %s', $sub));
            return $pathname ? $sub : file_get_contents($sub);
        }
        if (file_exists($level)) {
            array_push($this->config['error'], sprintf('Foi adicionado o level: %s', $level));
            return $pathname ? $level : file_get_contents($level);
        }
        array_push($this->config['error'], sprintf('Ficheiro nao encontrado: %s', $file));
        return '';
    }

    function setFile ($file) {
        global $path;
        $this->sendError();
        $file = strtolower($file);
        $this->config['file'] = $this->findFile($file);
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
    
    function includeFiles ($id, $file) {
        $idFile = strtolower($file);
        if ($filefound = $this->findFile($file, 1)) {
            $this->config['files'][$id] = $filefound;
            $line = sprintf("Foi incluido o ficheiro principal %s", $this->config['path'] . TPLPATH . $idFile . TPLEXT);
        } else {
            $line = sprintf("(principal) O ficheiro %s n&atilde;o existe", $this->config['path'] . TPLPATH . $idFile . TPLEXT);
        }
        array_push($this->config['error'], $line);
    }
    
    function includeHtml ($html) {
        $this->config['file'] .= $html;
        $this->replaceVar();
        $this->checkblocks('BLOCK');
        $this->checkblocks('IF');
        $this->checkblocks('LOOP');
        $this->config['file'] = preg_replace('#\{([A-Z0-9_]*)\}#s', '', $this->config['file']);
    }
    
    function sendOut () {
        global $mysql;
        
        if (!defined('DEBUG') || !DEBUG) {
            $this->config['file'] = str_replace(array("\r\n", "\r", "\n"), array('', '', ''), $this->config['file']);
        }
        
        eval(" ?>" . $this->config['file'] . "<?php ");
        
        if (defined('DEBUG') && DEBUG) {
            echo '<div class="msgError"><table id="mysql">';
            foreach ($this->config['error'] as $index => $error) {
                printf('<tr><td>%s</td></tr>', $error);
            }
            $constants = get_defined_constants(true);
            foreach ($constants['user'] as $constant => $value) {
                printf('<tr><td>%s: %s</td></tr>', $constant, $value);
            }
            echo '</table></div>';
        }
    }
    
    function sendError () {
        $this->config['error'] = array_reverse($this->config['error']);
        return;
        while ($error = array_pop($this->config['error'])) {
            if (constant('DEBUG') == '1')
                $this->setVar('MSG_ERROR', sprintf('%s<br>', $error));
            else 
                $this->setVar('MSG_INFO', $error);
        }
    }
    
    function addError ($info) {
        array_push($this->config['error'], $info);
    }
    
    function returnFile ($file) {
        $lines = file_get_contents($this->config['path'] . TPLPATH . $file . TPLEXT);
        $output .= $this->replaceVar ($lines);
        return $output;
    }
}
