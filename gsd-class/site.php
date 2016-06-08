<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;
defined('GVALID') or die;

class site
{
    public $name, $email, $ga, $gtm, $fb, $uri, $page, $main, $startpoint, $pagemodules, $pageextra, $layout, $protocol, $isFrontend, $options;
    protected $path;

    const VERSION = '1.7.2';

    public function __construct()
    {
        global $mysql, $tpl;

        $this->protocol = (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://');

        $this->startpoint = 'index';
        $this->main = '';
        $this->options = array();

        $mysql->reset()
            ->select()
            ->from('options')
            ->order('index')
            ->exec();

        foreach ($mysql->result() as $option) {
            $name = $option->name;
            $this->{$name} = $option->value;

            if ($option->type === 'image') {
                $image = new image(array('iid' => $option->value, 'width' => 'auto', 'height' => 'auto'));
                $tpl->setvar('SITE_'.strtoupper($name), $image);
            } else {
                $tpl->setvar('SITE_'.strtoupper($name), $option->value);
            }
            $this->options[$name] = array('type' => $option->type, 'value' => $option->value, 'label' => $option->label);
        }

        define('DEBUG', @$this->options['debug']['value']);
        $tpl->setcondition('DEBUG', !!@$this->options['debug']['value']);

        $pattern = '/(\?)(.*)/';
        $this->uri = rtrim(preg_replace($pattern, '', $_SERVER['REQUEST_URI']), '/');

        $this->uri = $this->uri ? $this->uri : '/';

        $this->path();
        $this->isFrontend = @$this->path[0] !== 'admin';
        $tpl->setcondition('CMS', !$this->isFrontend);

        if ($this->isFrontend) {
            $tpl->setvar('HTML_CLASS', 'frontend');
            $this->page();

            if (@$this->options['maintenance']['value']) {
                $this->startpoint = 'maintenance';
            }
        }
    }

    public function path()
    {
        $path = explode('/', $this->uri);

        array_shift($path);

        $this->path = $path;
    }

    public function page()
    {
        global $tpl, $mysql, $config;

        if (!IS_INSTALLED) {
            return;
        }

        $mysql->reset()
            ->select('destination')
            ->from('redirect')
            ->where('`from` = ?')
            ->values($this->uri)
            ->exec();

        if ($mysql->total) {
            redirect($mysql->singleresult(), 301);
        }

        $mysql->reset()
            ->select()
            ->from('pages')
            ->join('layouts', 'LEFT')
            ->on('layouts.lid = pages.lid');
        
        if (@$this->path[0] === 'p' && is_numeric(@$this->path[1])) {
            $mysql->where('published IS NOT NULL AND pages.deleted IS NULL AND pid = ?')
                    ->values($this->path[1]);
        } else {
            $mysql->where('published IS NOT NULL AND pages.deleted IS NULL AND BINARY beautify = ?')
                ->values($this->uri);
        }
        
        $mysql->limit(0, 1)
            ->exec();

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
                'PAGE_OG_IMAGE' => $page->og_image ? $this->protocol.$_SERVER['HTTP_HOST'].ASSETPATHURL.'images/'.$page->og_image : '',
                'PAGE_BODY' => $page->body,
            ));

            $this->main = trim(str_replace('.html', '', $page->file));

            $this->fetchModules($page->pid);

            $this->fetchExtra($page->pid);
        } else {
            http_response_code(404);
            $this->startpoint = '404';
            $tpl->setvar('PAGE_TITLE', $this->name);
        }
        $tpl->setvar('PAGE_CANONICAL', $this->protocol.$_SERVER['HTTP_HOST'].$this->uri);
    }

    public function fetchModules($pid)
    {
        global $mysql;

        $mysql->reset()
            ->select()
            ->from('pagemodules AS pm')
            ->join('layoutsections AS ls')
            ->on('ls.lsid = pm.lsid')
            ->where('pid = ?')
            ->values($pid)
            ->exec();

        if ($mysql->total) {
            $pagemodules = array();
            foreach ($mysql->result() as $module) {
                $pagemodules[@$module->label] = $module->data;
            }
            $this->pagemodules = $pagemodules;
        }
    }

    public function fetchExtra($pid)
    {
        global $mysql;

        $mysql->reset()
            ->select()
            ->from('pages_extra')
            ->where('pid = ?')
            ->values($pid)
            ->exec();

        if ($mysql->total) {
            $pageextra = array();
            foreach ($mysql->result() as $module) {
                $pageextra[$module->name] = $module->value;
            }
            $this->pageextra = $pageextra;
        }
    }

    public function arg($pos)
    {
        return $this->a($pos);
    }

    public function a($pos)
    {
        return @$this->path[$pos];
    }

    public function param($name = '', $session = 0)
    {
        return $this->p($name, $session);
    }

    public function p($name = '', $session = 0)
    {
        $type = $session ? $_SESSION : $_REQUEST;

        return $name ? @$type[$name] : $type;
    }

    public function searchpage($givenpath)
    {
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
