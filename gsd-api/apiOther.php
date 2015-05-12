<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.2
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### OTHER
namespace GSD\Api;

use GSD;

class apiOther
{
    public function outputDoc($table, $input, $returnFields)
    {
        global $mysql, $api;

        $output = array();
        $output['input'] = $input;
        $mysql->statement(sprintf('SHOW FULL COLUMNS FROM %s;', $table));

        foreach ($mysql->result() as $field) {
            if (in_array($field->Field, $returnFields)) {
                $output['output'][$field->Field] = $field->Comment;
            }
        }

        return $output;
    }

    public function paginatorLayouts($options)
    {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('lid', 'name', 'created', 'creator_id', 'creator_name');

        $mysql->reset()
            ->from('layouts AS l')
            ->join('users AS u', 'LEFT')
            ->on('l.creator = u.uid');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('l.title like "%%%s%%"', $_REQUEST['search']));
        }

        $mysql->order('lid');

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->select('l.*, u.name AS creator_name, u.uid AS creator_id')
            ->limit($paginator->pageLimit(), $numberPerPage)
            ->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = '('.timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]).')';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string) $paginator;
        } else {
            $output = array('error' => 0, 'message' => lang('LANG_NO_LAYOUTS'));
        }

        return $output;
    }

    public function paginatorPages($options)
    {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('pid', 'title', 'beautify', 'created', 'creator_id', 'creator_name', 'index');

        $mysql->reset()
            ->from('pages AS p')
            ->join('users AS u', 'LEFT')
            ->on('p.creator = u.uid')
            ->join('pages AS pp', 'LEFT')
            ->on('p.parent = pp.pid');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('p.title like "%%%s%%"', $_REQUEST['search']));
        }

        $mysql->order('p.index');

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->select('p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name')
            ->limit($paginator->pageLimit(), $numberPerPage)
            ->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = '('.timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]).')';
                $array['unpublished'] = $row->published ? '' : '<br>('.lang('LANG_UNPUBLISHED').')';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string) $paginator;
        }

        return $output;
    }

    public function paginatorUsers($options)
    {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('name', 'uid', 'created');
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $_fields = 'u.*';

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(', %s', $field);
                array_push($returnFields, $field);
            }
        }

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->reset()
            ->from('users AS u');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('u.name like "%%%s%%"', $_REQUEST['search']));
        }

        $mysql->order('u.uid');

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->select($_fields)
            ->limit($paginator->pageLimit(), $numberPerPage)
            ->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $last_login = explode(' ', @$row->last_login);
                $array['created'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
                $array['last_login'] = @$last_login[1] ? timeago(dateDif($last_login[0], date('Y-m-d', time())), @$last_login[1]) : lang('LANG_NEVER');
                $array['disabled'] = $row->disabled ? '<br>('.lang('LANG_DISABLED').')' : '';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string) $paginator;
        }

        return $output;
    }

    public function paginatorImages($options)
    {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('iid', 'description', 'creator_id', 'creator_name');

        $mysql->reset()
            ->from('images AS i')
            ->join('users AS u', 'LEFT')
            ->on('i.creator = u.uid');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('tags like "%%%s%%"', $_REQUEST['search']));
        }

        $mysql->order('i.iid');

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->select('i.*, i.creator AS creator_id, u.name AS creator_name')
            ->limit($paginator->pageLimit(), $numberPerPage)
            ->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
                $array['asset'] = @$row->width ? (string) new GSD\image(array('iid' => $row->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $array['size'] = sprintf('<strong>%s x %s</strong><br>%s', $row->width, $row->height, $row->size);
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string) $paginator;
        }

        return $output;
    }

    public function paginatorDocuments($options)
    {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('did', 'description', 'creator_id', 'creator_name');

        $mysql->reset()
            ->from('documents AS d')
            ->join('users AS u', 'LEFT')
            ->on('d.creator = u.uid');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('tags like "%%%s%%"', $_REQUEST['search']));
        }

        $mysql->order('d.did');

        $paginator = new GSD\paginator($numberPerPage, $page);

        $mysql->select('d.*, d.creator AS creator_id, u.name AS creator_name')
            ->limit($paginator->pageLimit(), $numberPerPage)
            ->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
                $array['asset'] = $row->name;
                $array['size'] = $row->size;
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string) $paginator;
        }

        return $output;
    }

    public function defaultValues($row, $returnFields)
    {
        $array = array();

        foreach ($row as $visible => $value) {
            if (in_array($visible, $returnFields)) {
                $array[$visible] = $value;
            }
        }

        return $array;
    }

    public function removeElements($table, $list)
    {
        global $mysql, $api;

        $list = explode(',', $list);
        $deleted = array();
        $field = substr($table, 0, 1).'id';

        foreach ($list as $id) {
            $mysql->reset()
                ->delete()
                ->from($table)
                ->where($field.' = ?')
                ->values($id)
                ->exec();

            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }
}
