<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class site {

    public $name, $email, $ga, $fb, $uri, $page, $main, $startpoint, $pagemodules, $layout, $protocol, $isFrontend;
    protected $path;

    public function __construct () {
        global $mysql, $tpl;
        
        $this->protocol = (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://');

        $this->startpoint = 'index';
        $this->main = '';

        $mysql->statement('SELECT * FROM options;');

        foreach ($mysql->result() as $option) {
            $name = str_replace('gsd-', '', $option->name);
            $this->{str_replace(array('_image', '_select'), '', $name)} = $option->value;

            if (strpos($name, '_image') !== false) {
                $image = new image(array('iid' => $option->value, 'width' => 'auto', 'height' => 'auto'));
                $name = str_replace(array('_image', '_select'), '', $name);
                $tpl->setvar('SITE_' . strtoupper($name), $image);
            } else {
                $name = str_replace(array('_image', '_select'), '', $name);
                $tpl->setvar('SITE_' . strtoupper($name), $option->value);
            }
        }

        $pattern = '/(\?)(.*)/';
        $this->uri = preg_replace($pattern, '', $_SERVER['REQUEST_URI']);

        $this->path();
        $this->isFrontend = $this->path[0] !== 'admin';
        $tpl->setcondition('CMS', !$this->isFrontend);

        if ($this->isFrontend) {
            $this->page();
        }
    }

    public function path () {
        $path = explode('/', $this->uri);

        array_shift($path);

        $this->path = $path;
    }

    public function page () {
        global $tpl, $mysql, $config;

        $mysql->statement('SELECT destination FROM redirect WHERE `from` = :uri', array(':uri' => $this->uri));
        if ($mysql->total) {
            header('Location: ' . $mysql->singleresult(), true, 301);
            exit;
        }

        $mysql->statement('SELECT *
        FROM pages
        LEFT JOIN layouts ON layouts.lid = pages.lid
        WHERE published IS NOT NULL AND BINARY beautify = ? LIMIT 0, 1;', array($this->uri));

        if ($mysql->total) {

            $page = $mysql->singleline();

            $this->page = $page;
            $this->layout = $page->file;

            $tpl->setvars(array(
                'PAGE_TITLE' => $page->title,
                'PAGE_DESCRIPTION' => $page->description,
                'PAGE_KEYWORDS' => $page->keywords,
                'PAGE_OG_TITLE' => $page->og_title ? $page->og_title : $page->title,
                'PAGE_OG_DESCRIPTION' => $page->og_description,
                'PAGE_OG_IMAGE' => $this->protocol . $_SERVER['HTTP_HOST'] . ASSETPATHURL . 'images/' . $page->og_image,
                'PAGE_CANONICAL' => $this->protocol . $_SERVER['HTTP_HOST'] . '/' . $this->uri
            ));

            $this->main = trim(str_replace('.html', '', $page->file));

            $mysql->statement('SELECT *
            FROM pagemodules AS pm
            JOIN layoutsections AS ls ON ls.lsid = pm.lsid
            WHERE pid = ?;', array($page->pid));

            if ($mysql->total) {
                $pagemodules = array();
                foreach ($mysql->result() as $module) {
                    $pagemodules[@$module->label] = $module->data;
                }
                $this->pagemodules = $pagemodules;
            }

        } else {
            $this->startpoint = '404';
            $tpl->setvar('PAGE_TITLE', $this->name);
        }
        $tpl->setvar('PAGE_CANONICAL', $this->protocol . $_SERVER['HTTP_HOST'] . $this->uri);
    }

    public function arg ($pos) {

        return @$this->path[$pos];
    }

    public function param ($name) {

        return @$_REQUEST[$name];
    }

    public function searchpage ($givenpath) {
        $temp = $this->uri;
        $this->startpoint = 'index';
        $this->main = '';

        $this->uri = $givenpath;
        $this->path();
        $this->page();

        $this->uri = $temp;
        $this->path();
    }
}
