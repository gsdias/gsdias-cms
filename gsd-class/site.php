<?php
/*************************************
* File with user class information  *
*************************************/

class site {

    public $name, $email, $ga, $fb, $uri, $page, $main, $startpoint, $pagemodules, $layout, $protocol;
    protected $path;

    public function __construct () {
        global $mysql, $tpl;
        
        $this->protocol = (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://');

        $this->startpoint = 'index';
        $this->main = '';

        $mysql->statement('SELECT * FROM options;');

        foreach ($mysql->result() as $option) {
            $name = str_replace('gsd-', '', $option['name']);
            $this->{$name} = $option['value'];
            if (strpos($name, '_image') !== false) {
                if ($option['value']) {
                    $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($option['value']));
                    $image = $mysql->singleline();
                    $image = new image(array('iid' => $image['iid'], 'width' => @$image['width'], 'height' => @$image['height']));

                    $tpl->setvar('SITE_' . strtoupper($name), $image);
                }
            } else {
                $tpl->setvar('SITE_' . strtoupper($name), $option['value']);
            }
        }

        $pattern = '/(\?)(.*)/';
        $this->uri = preg_replace($pattern, '', $_SERVER['REQUEST_URI']);

        $this->path();
        $this->page();
    }

    public function path () {
        $path = explode("/", $this->uri);

        array_shift($path);

        $this->path = $path;
    }

    public function page () {
        global $tpl, $mysql, $config;

        $mysql->statement('SELECT destination FROM redirect WHERE `from` = :uri', array(':uri' => $this->uri));
        if ($mysql->total) {
            header("Location: " . $mysql->singleresult(), true, 301);
            exit;
        }

        $levels = explode('/', $this->uri);
        $urls = array($this->uri);

        if (sizeof($levels) > 2) {
            while(sizeof($levels)) {
                array_pop($levels);
                $url = implode('/', $levels);
                if ($url) {
                    $urls[] = $url;
                }
            }
        }

        $questions = str_repeat('url = ? OR ', sizeof($urls) - 1);
        $questions .= 'url = ?';

        $mysql->statement(sprintf('SELECT *
        FROM pages
        LEFT JOIN layouts ON layouts.lid = pages.lid
        WHERE published IS NOT NULL AND (%s) LIMIT 0, 1;', $questions), $urls);

        if ($mysql->total) {

            $page = $mysql->singleline();

            $this->page = $page;
            $this->layout = $page['file'];

            $tpl->setvars(array(
                'PAGE_TITLE' => $page['title'],
                'PAGE_DESCRIPTION' => $page['description'],
                'PAGE_KEYWORDS' => $page['keywords'],
                'PAGE_OG_TITLE' => $page['og_title'] ? $page['og_title'] : $page['title'],
                'PAGE_OG_DESCRIPTION' => $page['og_description'],
                'PAGE_OG_IMAGE' => $this->protocol . $_SERVER['HTTP_HOST'] . '/gsd-assets/images/' . $page['og_image'],
                'PAGE_CANONICAL' => $this->protocol . $_SERVER['HTTP_HOST'] . '/' . $this->uri
            ));
            $this->main = trim(str_replace('.html', '', $page['file']));

            $mysql->statement('SELECT *
            FROM pagemodules AS pm
            JOIN layoutsections AS ls ON ls.lsid = pm.lsid
            WHERE pid = ?;', array($page['pid']));

            if ($mysql->total) {
                $pagemodules = array();
                foreach ($mysql->result() as $module) {
                    $pagemodules[$module['name']] = $module['data'];
                }
                $this->pagemodules = $pagemodules;
            }

        } else {
            $this->startpoint = '404';
            $tpl->setvar('PAGE_TITLE', $this->name);
        }
        $tpl->setvar('PAGE_CANONICAL', $this->protocol . $_SERVER['HTTP_HOST'] . $this->uri
        );
    }

    public function arg ($pos) {

        return @$this->path[$pos];
    }
}
