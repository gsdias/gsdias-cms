<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### OTHER

class apiOther {

    function outputDoc ($table, $input, $returnFields) {
        global $mysql, $api;

        $output = array();
        $output['input'] = $input;
        $mysql->statement(sprintf('SHOW FULL COLUMNS FROM %s;', $table));

        foreach ($mysql->result() as $field) {
            if (in_array($field->Field, $returnFields))
                $output['output'][$field->Field] = $field->Comment;
        }
        return $output;
    }

    function paginatorLayouts($options) {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('lid', 'name', 'created', 'creator_id', 'creator_name');
        $search = @$_REQUEST['search'] ? sprintf(' WHERE p.title like "%%%s%%" ', $_REQUEST['search']) : '';
        $fromsql = sprintf(' FROM layouts
        LEFT JOIN users AS u ON layouts.creator = u.uid
        ORDER BY lid ', $search);

        $paginator = new GSD\paginator($fromsql, $numberPerPage, $page);

        $mysql->statement('SELECT layouts.*, u.name AS creator_name, u.uid AS creator_id ' . $fromsql . $paginator->pageLimit());

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = '(' . timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]) . ')';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string)$paginator;
        }

        return $output;
    }

    function paginatorPages($options) {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('pid', 'title', 'beautify', 'created', 'creator_id', 'creator_name', 'index');

        $search = @$_REQUEST['search'] ? sprintf(' WHERE p.title like "%%%s%%" ', $_REQUEST['search']) : '';

        $sql = ' FROM pages AS p
            LEFT JOIN users AS u ON p.creator = u.uid
            LEFT JOIN pages AS pp ON p.parent = pp.pid '
            . $search .
            'ORDER BY p.`index` ';

        $paginator = new GSD\paginator($sql, $numberPerPage, $page);

        $mysql->statement('SELECT p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name' . $sql . $paginator->pageLimit());

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = '(' . timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]) . ')';
                $array['unpublished'] = $row->published ? '' : '<br>(' . lang('LANG_UNPUBLISHED') . ')';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string)$paginator;
        }

        return $output;
    }

    function paginatorUsers($options) {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('name', 'uid', 'created');
        $search = @$_REQUEST['search'] ? sprintf(' WHERE p.title like "%%%s%%" ', $_REQUEST['search']) : '';
        $fromsql = sprintf(' FROM users
        ORDER BY users.uid ', $search);
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $_fields = 'users.*, users.creator';

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(", %s", $field);
                array_push($returnFields, $field);
            }
        }

        $paginator = new GSD\paginator($sql, $numberPerPage, $page);

        $mysql->statement('SELECT ' . $_fields . $fromsql . $paginator->pageLimit());

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $last_login = explode(' ', @$row->last_login);
                $array['created'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
                $array['last_login'] = timeago(dateDif($last_login[0], date('Y-m-d', time())), $last_login[1]);
                $array['disabled'] = $row->disabled ? '<br>(' . lang('LANG_DISABLED') . ')' : '';
                array_push($output['data']['list'], $array);
            }

            $output['data']['paginator'] = (string)$paginator;
        }

        return $output;
    }

    function paginatorImages($options) {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('iid', 'description', 'creator_id', 'creator_name');

        $search = @$_REQUEST['search'] ? sprintf(' WHERE tags like "%%%s%%" ', $_REQUEST['search']) : '';

        $sql = ' FROM images
            LEFT JOIN users AS u ON images.creator = u.uid '
            . $search .
            'ORDER BY images.iid ';

        $paginator = new GSD\paginator($sql, $numberPerPage, $page);
        
        $mysql->statement('SELECT images.*, images.creator AS creator_id, u.name AS creator_name' . $sql . $paginator->pageLimit());

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = $this->defaultValues($row, $returnFields);

                $created = explode(' ', @$row->created);
                $array['created'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
                $array['asset'] = @$row->width ? (string)new GSD\image(array('iid' => $row->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $array['size'] = sprintf('<strong>%s x %s</strong><br>%s', $row->width, $row->height, $row->size);
                array_push($output['data']['list'], $array);
            }
            
            $output['data']['paginator'] = (string)$paginator;
        }

        return $output;
    }

    function paginatorDocuments($options) {
        global $mysql, $api;

        $page = $options['page'];
        $numberPerPage = $options['numberPerPage'];
        $output = $options['output'];

        $returnFields = array('did', 'description', 'creator_id', 'creator_name');

        $search = @$_REQUEST['search'] ? sprintf(' WHERE tags like "%%%s%%" ', $_REQUEST['search']) : '';

        $sql = ' FROM documents
            LEFT JOIN users AS u ON documents.creator = u.uid '
            . $search .
            'ORDER BY documents.did ';

        $paginator = new GSD\paginator($sql, $numberPerPage, $page);

        $mysql->statement('SELECT documents.*, documents.creator AS creator_id, u.name AS creator_name' . $sql . $paginator->pageLimit());

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

            $output['data']['paginator'] = (string)$paginator;
        }

        return $output;
    }

    function defaultValues ($row, $returnFields) {
        $array = array();

        foreach ($row as $visible => $value) {
            if (in_array($visible, $returnFields)) {
                $array[$visible] = $value;
            }
        }

        return $array;
    }
}
