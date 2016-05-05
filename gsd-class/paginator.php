<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.2
 */
namespace GSD;

class paginator
{
    public $total, $page, $numberPerPage;

    private $tpl;

    public function __construct($numberPerPage = 30, $page = 1)
    {
        $this->tpl = new tpl();

        $this->numberPerPage = $numberPerPage;
        $this->page = is_numeric($page) ? $page : 1;

        $this->pageTotal();

        $this->tpl->setcondition('PAGINATOR', $this->total > 1);
        $this->pageGenerator();

        $this->tpl->includeFiles('MAIN', '_paginator');
        $this->tpl->setFile('_paginator');

        return;
    }

    private function pageTotal()
    {
        global $mysql;

        $mysql->select(sprintf('floor(count(*) / %d) AS p, mod(count(*), %d) AS r', $this->numberPerPage, $this->numberPerPage))
            ->exec();

        $result = $mysql->singleline();
        $pages = @$result->p ? $result->p : 0;
        $remain = @$result->r ? $result->r : 0;
        $pages = $remain > 0 ? ++$pages : $pages;

        $this->total = $pages;
    }

    public function getPageTotal()
    {
        return $this->total;
    }

    public function pageLimit()
    {
        $limit = ($this->page - 1) * $this->numberPerPage;
        $limit = $limit < 0 ? 0 : $limit;

        return $limit;
    }

    private function pageGenerator()
    {
        $options = array(
            'PREV' => $this->page > 1 ? $this->page - 1 : 1,
            'NEXT' => $this->page < $this->total ? $this->page + 1 : $this->total,
            'CURRENT' => $this->page,
            'TOTAL' => $this->total,
            'LAST' => $this->total,
        );

        $this->generatepaginator($options);
    }

    private function generatepaginator($pages)
    {
        $first_page = new anchor(array('text' => '&lt; {LANG_FIRST}', 'href' => '?page=1'));
        $prev_page = new anchor(array('text' => lang('LANG_PREVIOUS'), 'href' => '?page='.$pages['PREV']));
        $next_page = new anchor(array('text' => lang('LANG_NEXT'), 'href' => '?page='.$pages['NEXT']));
        $last_page = new anchor(array('text' => '{LANG_LAST} &gt;', 'href' => '?page='.$pages['LAST']));
        $this->tpl->setvars(array(
            'FIRST_PAGE' => $first_page,
            'PREV_PAGE' => $prev_page,
            'NEXT_PAGE' => $next_page,
            'LAST_PAGE' => $last_page,
            'CURRENT_PAGE' => $pages['CURRENT'],
            'TOTAL_PAGES' => $pages['TOTAL'],
        ));
    }

    public function __toString()
    {
        return (string) $this->tpl;
    }
}
