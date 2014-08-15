<?php
/*************************************
* File with user class information  *
*************************************/

class site {

    public $name, $email, $ga, $fb, $uri, $page;
    protected $path;

    public function __construct () {
        global $mysql, $tpl;

        $mysql->statement('SELECT * FROM options;');

        foreach ($mysql->result() as $option) {
            $name = str_replace('gsd-', '', $option['name']);
            $this->{$name} = $option['value'];
            if (strpos($name, '_image') !== false) {
                $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($option['value']));
                $image = $mysql->singleline();
                $image = new image(array('src' => sprintf('/gsd-assets/images/%s.%s', @$image['iid'], @$image['extension']), 'width' => @$image['width'], 'height' => @$image['height']));

                $tpl->setvar('SITE_' . strtoupper($name), $image);
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
        global $tpl, $mysql, $startpoint;

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

        $mysql->statement(sprintf('SELECT * FROM pages WHERE published IS NOT NULL AND (%s) LIMIT 0, 1;', $questions), $urls);

        if ($mysql->total) {

            $page = $mysql->singleline();

            $this->page = $page;

            $tpl->setvars(array(
                'PAGE_TITLE' => $page['title'],
                'PAGE_DESCRIPTION' => $page['description'],
                'PAGE_KEYWORDS' => $page['keywords'],
                'PAGE_OG_TITLE' => $page['og_title'],
                'PAGE_OG_DESCRIPTION' => $page['og_description'],
                'PAGE_OG_IMAGE' => $page['og_image'],
                'PAGE_CANONICAL' => $_SERVER['HTTP_HOST'] . '/' . $this->uri
            ));

        } else {
            $startpoint = '404';
            $tpl->setvars(array(
                'PAGE_TITLE' => $this->name,
                'PAGE_CANONICAL' => (stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->uri
            ));
        }
    }

    public function arg ($pos) {

        return @$this->path[$pos];
    }
}
