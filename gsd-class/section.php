<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

abstract class section implements isection {

    public $item = array();

    public function __construct ($id = 0) {

        return 0;
    }

    public function getlist ($options) {
        global $tpl;

        $numberPerPage = @$options['numberPerPage'] ? $options['numberPerPage'] : 10;
        $results = empty($options['results']) ? array() : $options['results'];
        $sql = @$options['sql'];
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $list = array();

        foreach ($results as $line) {
            $item = array();

            foreach ($fields as $field) {
                if (property_exists($line, $field)) {
                    $item[strtoupper($field)] = $line->{$field};
                }
            }

            $created = explode(' ', @$line->created);
            $item['CREATED'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);

            $list[] = $item;
        }

        $tpl->setcondition(strtoupper($this->tablename($this)) . '_EXIST', !empty($list));

        $tpl->setarray(strtoupper($this->tablename($this)), $list);

        $pages = pageGenerator($sql);

        $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);

        $this->generatepaginator($pages);

        return array('list' => $list, 'results' => $results);
    }

    public function getcurrent ($item = array()) {
        global $site, $tpl;

        if (empty($item)) {
            header("Location: /admin/" . $site->arg(1), true, 302);
            exit;
        }

        $this->item = $item;

        $fields = array();

        foreach ($item as $field => $value) {
            $fields['CURRENT_' . strtoupper($this->tablename()) . '_'. strtoupper($field)] = $value;
        }

        $tpl->setvars($fields);

        return array('item' => $item, 'fields' => $fields);
    }

    public function generatefields () {
        global $tpl, $mysql;

        $func = $this->tablename() . 'fields';
        $item = $this->item;

        $sectionextrafields = function_exists($func) ? $func() : array();
        if (sizeof($sectionextrafields)) {
            $extrafields = array();

            foreach ($sectionextrafields['list'] as $key => $extrafield) {

                $extraclass = '';

                switch ($sectionextrafields['types'][$key]) {
                    case 'image':
                    $image = new image(array(
                        'iid' => @$item->{$extrafield},
                        'height' => '100',
                        'width' => 'auto',
                        'class' => sprintf('preview %s', @$item->{$extrafield} ? '' : 'is-hidden')
                    ));

                    $partial = new tpl();
                    $partial->setvars(array(
                        'LABEL' => $sectionextrafields['labels'][$key],
                        'NAME' => $extrafield,
                        'IMAGE' => $image,
                        'VALUE' => @$item->{$extrafield} ? @$item->{$extrafield} : 0,
                        'EMPTY' => @$item->{$extrafield} ? 'is-hidden' : ''
                    ));
                    $partial->setfile('_image');

                    $field = $partial;
                    $extraclass = 'image';
                    break;
                    case 'select':
                    $field = new select(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'list' => $sectionextrafields['values'][$key],
                        'label' => $sectionextrafields['labels'][$key],
                        'selected' => @$item->{$extrafield}
                    ));
                    break;
                    default:
                    $field = (string)new input(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'value' => @$item->{$extrafield},
                        'label' => $sectionextrafields['labels'][$key]
                    ));
                    break;
                }

                $extrafields[] = array('FIELD' => $field, 'EXTRACLASS' => $extraclass);
            }

            $tpl->setarray('FIELD', $extrafields);
            $tpl->setcondition('EXTRAFIELDS');
        }
    }

    public function generatepaginator ($pages) {
        global $tpl;

        $first_page = new anchor(array('text' => '&lt; {LANG_FIRST}', 'href' => '?page=1'));
        $prev_page = new anchor(array('text' => lang('LANG_PREVIOUS'), 'href' => '?page=' . $pages['PREV']));
        $next_page = new anchor(array('text' => lang('LANG_NEXT'), 'href' => '?page=' . $pages['NEXT']));
        $last_page = new anchor(array('text' => '{LANG_LAST} &gt;', 'href' => '?page=' . $pages['LAST']));
        $tpl->setvars(array(
            'FIRST_PAGE' => $first_page,
            'PREV_PAGE' => $prev_page,
            'NEXT_PAGE' => $next_page,
            'LAST_PAGE' => $last_page,
            'CURRENT_PAGE' => $pages['CURRENT'],
            'TOTAL_PAGES' => $pages['TOTAL']
        ));
    }

    public function add ($defaultfields, $defaultsafter = array(), $defaultvalues = array()) {
        global $mysql;
        
        $section = $this->tablename();

        $extrafields = $this->extrafields ();

        $fields = array_merge($defaultfields, $extrafields);

        $values = array();

        foreach ($fields as $field) {
            $values[] = $_REQUEST[$field];
        }

        $fields = array_merge($fields, $defaultsafter);

        $values = array_merge($values, $defaultvalues);    

        $questions = str_repeat(", ? ", sizeof($fields));

        $mysql->statement(sprintf('INSERT INTO %s (%s) values (%s);', $section, implode(', ', $fields), substr($questions, 2)), $values);
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'errmsg' => $mysql->errmsg, 'id' => $mysql->lastinserted());
    }
    
    public function edit ($defaultfields, $defaultsafter = array(), $defaultvalues = array()) {
        global $mysql, $site;
        
        $section = $this->tablename();
        
        $extrafields = $this->extrafields ();

        $fields = '';

        $values = array();

        $allfields = array_merge($defaultfields, $extrafields);

        foreach ($allfields as $field) {
            $fields .= sprintf(", `%s` = ?", $field);
            $values[] = $field == 'password' ? md5(@$_REQUEST[$field]) : @$_REQUEST[$field];
        }

        foreach ($defaultsafter as $index => $field) {
            $fields .= sprintf(", `%s` = ?", $field);
            $values[] = $field == 'password' ? md5($_REQUEST[$defaultvalues[$index]]) : $defaultvalues[$index];
        }

        $values[] = $site->arg(2);

        $mysql->statement(sprintf('UPDATE %s SET %s WHERE %sid = ?;', $section, substr($fields, 2), substr($section, 0, 1)), $values);
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
    }
    
    public function remove () {
        global $mysql, $site;
        
        $section = $this->tablename();
        
        $mysql->statement(sprintf('DELETE FROM %s WHERE %sid = ?;', $section, substr($section, 0, 1)), array($site->arg(2)));
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
    }

    private function tablename () {
        $class = get_class($this);

        return substr($class, 0, 6) === 'client' ? substr($class, 6) : $class;
    }

    protected function extrafields () {

        $section = $this->tablename();

        $_fields = $section . 'fields';

        $fields = function_exists($_fields) ? $_fields() : array('list' => array());

        return $fields['list'];
    }
}
