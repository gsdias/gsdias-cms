<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class tpl
{
    public $config = array(
        'vars' => array(),
        'files' => array(),
        'error' => array(),
        'array' => array(),
        'conditions' => array(),
        'paths' => array(),
        'file' => '',
        'path' => '',
    );

    public function __construct()
    {
        $this->config['paths'] = array(ROOTPATH.'gsd-tpl/_shared/%s'.TPLEXT, ROOTPATH.'gsd-tpl/_modules/%s'.TPLEXT);
    }

    /**
     * @desc Saves the value of a template's variable
     *
     * @param string $id    - given id
     * @param string $value - given value
     *
     * @return nothing
     */
    public function setVar($id, $value = '')
    {
        $this->config['vars'][$id] = @$this->config['vars'][$id].$value;
    }

    /**
     * @desc Returns the value of a template's variable
     *
     * @param string $id    - given id
     * @param string $value - given value
     *
     * @return nothing
     */
    public function getVar($id)
    {
        return @$this->config['vars'][$id];
    }

    /**
     * @desc Saves the value of a template's variable
     *
     * @param string $id    - given id
     * @param string $value - given value
     *
     * @return nothing
     */
    public function setVars($values = array())
    {
        if (!empty($values)) {
            foreach ($values as $id => $value) {
                $this->setvar($id, $value);
            }
        }
    }

    /**
     * @desc Replaces the value of a saved variable
     *
     * @param string $id    - given id
     * @param string $value - given value
     *
     * @return nothing
     */
    public function repVar($id, $value = '')
    {
        $this->config['vars'][$id] = $value;
    }

    /**
     * @desc Saves the value of a template's variable
     *
     * @param string $id    - given id
     * @param string $value - given value
     *
     * @return nothing
     */
    public function repVars($values = array())
    {
        if (!empty($values)) {
            foreach ($values as $id => $value) {
                $this->repvar($id, $value);
            }
        }
    }

    /**
     * @desc Saves an array of a template's loop
     *
     * @param string $id    - given id
     * @param array  $value - given list
     *
     * @return nothing
     */
    public function setArray($id, $value = array(), $merge = true)
    {
        $value = count($value) != count($value, 1) ? $value : array($value);

        if ($merge && !empty($this->config['array'][$id])) {
            $this->config['array'][$id] = array_merge($this->config['array'][$id], $value);
        } else {
            $this->config['array'][$id] = $value;
        }
    }

    /**
     * @desc Saves an array of a template's loop
     *
     * @param string $id    - given id
     * @param array  $value - given list
     *
     * @return nothing
     */
    public function setcondition($id, $value = 1)
    {
        $this->config['conditions'][$id] = $value;
    }

    /**
     * @desc
     *
     * @param string $path -
     *
     * @return nothing
     */
    public function setpaths($path = array())
    {
        $this->config['paths'] = array_merge($path, $this->config['paths']);
    }

    public function createImage($placeholder)
    {
        global $site, $mysql;

        $item = $site->pagemodules[$placeholder[0]];
        $item = unserialize($item);

        $item = new image(array(
            'iid' => @$item['list'][0][0]['value'],
            'width' => 'auto',
            'class' => @$item['list'][0][0]['class'],
            'style' => @$item['list'][0][0]['style'],
        ));

        return @$extra.$item;
    }

    public function createLists($placeholder)
    {
        global $site;

        $data = unserialize($site->pagemodules[$placeholder[0]]);
        $ul = '';
        if (gettype($data) == 'array' && sizeof($data)) {
            $ul .= sprintf('<ul class="%s">', $data['class']);
            foreach ($data['list'] as $items) {
                $li = '';
                if (gettype($items) == 'array' && sizeof($items)) {
                    $content = $this->populateLists($placeholder, $items);
                    if ($content) {
                        $ul .= $content;
                    }
                }
            }
            $ul .= '</ul>';
        }

        return $ul;
    }

    public function languagereplace()
    {
        $content = $this->config['file'];
        $list = array();
        preg_match_all('#{LANG_(.*?)}#s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $option = explode(' ', $match[1]);
            $replace = lang('LANG_'.$option[0], @$option[1]);

            $content = preg_replace(sprintf('#%s#s', $match[0]), $replace, $content, 1);
        }

        $this->config['file'] = $content;
    }

    public function populateLists($placeholder, $items)
    {
        $li = '';
        foreach ($items as $item) {
            if ($placeholder[2] == 'IMAGE' && $item['value']) {
                $image = new GSD\image(array(
                    'iid' => $item['value'],
                    'width' => 'auto',
                    'class' => $item['class'],
                    'style' => $item['style'],
                ));
                $li = sprintf('<li>%s</li>', $image);
            } elseif ($item['value']) {
                $li = sprintf('<li%s%s>%s</li>', $item['class'] ? sprintf(' class="%s"', $item['class']) : '', $item['style'] ? sprintf(' style="%s"', $item['style']) : '', $item['value']);
            }
        }

        return $li;
    }

    /**
     * @desc
     *
     * @param string $path -
     *
     * @return nothing
     */
    private function checkblocks($type = 'BLOCK')
    {
        global $mysql, $site;
        $list = array();

        $type = strtoupper($type);

        preg_match_all(sprintf('#<!-- %s (.*?) -->#s', $type), $this->config['file'], $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            array_push($list, $match[1]);
        }
        $this->languagereplace();
        while ($key = array_pop($list)) {
            if ($type == 'PLACEHOLDER') {
                $placeholder = explode(' ', $key);
                if ($placeholder[1] == 'LIST') {
                    $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->#s', $type, $key), $this->createLists($placeholder), $this->config['file'], 1);
                } elseif ($placeholder[1] == 'IMAGE') {
                    $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->#s', $type, $key), $this->createImage($placeholder), $this->config['file'], 1);
                } else {
                    $item = @$site->pagemodules[@$placeholder[0]];
                    $item = unserialize($item);
                    if (DEBUG) {
                        $extra = sprintf('<!-- DEBUG %s %s -->', $type, $key);
                    }
                    $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->#s', $type, $key), @$extra.$item['list'][0][0]['value'], $this->config['file'], 1);
                }
            } else {
                preg_match_all(sprintf('#<!-- %s %s -->(.*?)<!-- END%s %s -->#s', $type, $key, $type, $key), $this->config['file'], $matches, PREG_SET_ORDER);
                for ($i = 0; $i < count($matches); $i++) {
                    if (DEBUG) {
                        $extra = sprintf('<!-- DEBUG %s %s -->', $type, $key);
                    }
                    if ($type === 'LOOP') {
                        $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->.*?<!-- END%s %s -->#s', $type, $key, $type, $key), @$extra.$this->loopBlock($key, $matches[$i][1]), $this->config['file'], 1);
                    } else {
                        $this->config['file'] = preg_replace(sprintf('#<!-- %s %s -->.*?<!-- END%s %s -->#s', $type, $key, $type, $key), @$extra.$this->ifBlock($key, $matches[$i][1]), $this->config['file'], 1);
                    }
                }
            }
        }
    }

    /**
     * @desc Replaces the variable id for it's value
     *
     * @return nothing
     */
    private function replaceVar()
    {
        foreach ($this->config['vars'] as $id => $value) {
            $this->config['file'] = str_replace(sprintf('{%s}', $id), $value, $this->config['file']);
        }
    }

    /**
     *
     */
    private function loopBlock($blockid, $block)
    {
        $param = explode(' ', $blockid);
        $blockid = $param[0];
        $bloco = '';
        $_block = $block;
        if (isset($this->config['array'][$blockid])) {
            $info = $this->config['array'][$blockid];
            $c = 1;

            $start = sizeof($param) > 2 ? $param[1] : 1;
            $end = sizeof($param) > 1 ? $param[sizeof($param) - 1] : sizeof($info);

            $end = $end === 'END' ? sizeof($info) : $end;

            if ($start === 'END') {
                $start = sizeof($info) - $end + 1;
                $end = sizeof($info);
            }

            foreach ($info as $_array) {
                if ($c >= $start && $c <= $end) {
                    $_block = $block;
                    foreach ($_array as $_var => $_value) {
                        $_block = str_replace(sprintf('{%s_%s}', $blockid, $_var), $_value, $_block);
                    }
                    $bloco .= sprintf("%s\r\n", $_block);
                }
                $c++;
            }
        }

        return substr($bloco, 0, -2);
    }
    private function ifBlock($blockid, $block)
    {
        if (strpos($blockid, ' OR ')) {
            $list = explode(' ', $blockid);
            $detected = 0;
            foreach ($list as $blk) {
                if (substr($blk, 0, 1) == '!') {
                    $detected = @$this->config['conditions'][substr($blk, 1)] == 0 || $detected ? 1 : 0;
                } else {
                    $detected = @$this->config['conditions'][$blk] == 1 || $detected ? 1 : 0;
                }
            }

            return $detected ? $block : '';
        }
        if (strpos($blockid, ' AND ')) {
            $list = explode(' ', $blockid);
            $detected = 1;
            foreach ($list as $blk) {
                if ($blk == 'AND') {
                    continue;
                }
                if (substr($blk, 0, 1) == '!') {
                    $detected = @$this->config['conditions'][substr($blk, 1)] == 0 && $detected ? 1 : 0;
                } else {
                    $detected = $this->config['conditions'][$blk] == 1 && $detected ? 1 : 0;
                }
            }

            return $detected ? $block : '';
        }

        if (substr($blockid, 0, 1) == '!') {
            return !@$this->config['conditions'][substr($blockid, 1)] || @$this->config['conditions'][substr($blockid, 1)] == 0 ? $block : '';
        } else {
            return @$this->config['conditions'][$blockid] == 1 ? $block : '';
        }
    }

    private function findFile($file, $pathname = 0)
    {
        global $site;

        $path = explode('/', $file);
        $file = array_pop($path);
        $path = array_pop($path);

        if (isset($this->config['files'][$file])) {
            return $pathname ? '' : file_get_contents($this->config['files'][$file]);
        }

        $file = strtolower($file);

        foreach ($this->config['paths'] as $_path) {
            $cpath = sprintf($_path, $path ? $path : $file, $file);

            if (is_file($cpath)) {
                $this->addError('TPL: '.$cpath);

                return $pathname ? $cpath : file_get_contents($cpath);
            }
        }

        foreach ($this->config['paths'] as $_path) {
            $cpath = sprintf($_path, $site->arg(0), $file);

            if (is_file($cpath)) {
                $this->addError('TPL: '.$cpath);

                return $pathname ? $cpath : file_get_contents($cpath);
            }
        }

        return '';
    }

    public function setFile($file)
    {
        $file = strtolower($file);
        $this->config['file'] = $this->findFile($file);

        $this->sendError();

        preg_match_all('*\[[A-Z_\/]+\]*', $this->config['file'], $matches);
        while (sizeof($matches[0]) > 0) {
            foreach ($matches[0] as $file) {
                $value = str_replace(array('[', ']'), '', $file);
                $files = $this->findFile($value);
                $this->config['file'] = str_replace(sprintf('[%s]', $value), $files, $this->config['file']);
                $this->replaceVar();
                $this->checkblocks('PLACEHOLDER');
                $this->checkblocks('BLOCK');
                $this->checkblocks('IF');
                $this->checkblocks('LOOP');
            }
            preg_match_all('*\[[A-Z_\/]+\]*', $this->config['file'], $matches);
        }
        $this->replaceVar();
        $this->checkblocks('PLACEHOLDER');
        $this->checkblocks('BLOCK');
        $this->checkblocks('IF');
        $this->checkblocks('LOOP');
        $this->config['file'] = preg_replace('#\{([A-Z0-9_]*)\}#s', '', $this->config['file']);
    }

    public function includeFiles($id, $file = null)
    {
        $idFile = strtolower($file);

        if ($file) {
            if ($filefound = $this->findFile($idFile, 1)) {
                $this->config['files'][$id] = $filefound;
            } elseif (is_file($file)) {
                $this->config['files'][$id] = $file;
            }
        } else {
            unset($this->config['files'][$id]);
        }
    }

    public function includeHtml($html)
    {
        $this->config['file'] .= $html;
    }

    public function __toString()
    {
        global $mysql;

        if (!DEBUG) {
            $this->config['file'] = str_replace(array("\r\n", "\r", "\n"), array('', '', ''), $this->config['file']);
        }

        ob_start();

        eval(' ?>'.$this->config['file'].'<?php ');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    public function sendError()
    {
        $this->config['error'] = $this->config['error'];
        
        if (DEBUG) {
            $this->setarray('DEBUGLIST', $this->config['error']);
        }
    }

    public function addError($info)
    {
        array_push($this->config['error'], array('MSG' => $info));
    }

    public function returnFile($file)
    {

        $this->setcondition('DEBUG', DEBUG);
        $lines = file_get_contents($this->config['path'].TPLPATH.$file.TPLEXT);
        $output .= $this->replaceVar($lines);

        return $output;
    }
}
