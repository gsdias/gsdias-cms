<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

abstract class section implements isection
{
    public $item = array();
    public $permission = '';

    public function __construct($permission)
    {
        $this->permission = $permission;
        return 0;
    }

    public function getlist($options)
    {
        global $tpl;

        $results = empty($options['results']) ? array() : $options['results'];
        $fields = empty($options['fields']) ? array() : $options['fields'];
        $paginator = $options['paginator'];

        $tpl->setvar('SEARCH_VALUE', $options['search']);

        $list = array();

        $first = !@$_REQUEST['page'] || @$_REQUEST['page'] == 1 ? -1 : 0;
        $last = sizeof($results) < 11 || @$_REQUEST['page'] == $options['totalPages'] ? -1 : sizeof($results) - 1;

        foreach ($results as $index => $line) {
            $item = array();

            foreach ($fields as $field) {
                if (property_exists($line, $field)) {
                    $item[strtoupper($field)] = $line->{$field};
                }
            }

            $created = explode(' ', @$line->created);
            $item['CREATED'] = timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]);
            $item['ISHIDDEN'] = $index === $first || $index === $last ? ' class="is-hidden"' : '';

            $list[] = $item;
        }

        $tpl->setcondition(strtoupper($this->tablename($this)).'_EXIST', !empty($list));

        $tpl->setarray(strtoupper($this->tablename($this)), $list);

        $tpl->setvar('PAGINATOR', $paginator);

        return array('list' => $list, 'results' => $results);
    }

    public function getcurrent($item = array())
    {
        global $site, $tpl;

        if (empty($item)) {
            header('Location: /admin/'.$site->arg(1), true, 302);
            exit;
        }

        $this->item = $item;

        $fields = array();

        foreach ($item as $field => $value) {
            $fields['CURRENT_'.strtoupper($this->tablename()).'_'.strtoupper($field)] = $value;
        }

        $tpl->setvars($fields);

        return array('item' => $item, 'fields' => $fields);
    }

    public function generatefields()
    {
        global $tpl, $mysql;

        $func = $this->tablename().'fields';
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
                        'class' => sprintf('preview %s', @$item->{$extrafield} ? '' : 'is-hidden'),
                    ));

                    $partial = new tpl();
                    $partial->setvars(array(
                        'LABEL' => $sectionextrafields['labels'][$key],
                        'NAME' => $extrafield,
                        'IMAGE' => $image,
                        'VALUE' => @$item->{$extrafield} ? @$item->{$extrafield} : 0,
                        'EMPTY' => @$item->{$extrafield} ? 'is-hidden' : '',
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
                        'selected' => @$item->{$extrafield},
                    ));
                    break;
                    case 'checkbox':
                    $field = (string) new input(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'value' => @$item->{$extrafield},
                        'label' => $sectionextrafields['labels'][$key],
                        'type' => 'checkbox'
                    ));
                    $extraclass = 'checkbox';
                    break;
                    case 'textarea':
                    $field = (string) new textarea(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'value' => @$item->{$extrafield},
                        'label' => $sectionextrafields['labels'][$key]
                    ));
                    break;
                    default:
                    $field = (string) new input(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'value' => @$item->{$extrafield},
                        'label' => $sectionextrafields['labels'][$key],
                    ));
                    break;
                }

                $extrafields[] = array('FIELD' => $field, 'EXTRACLASS' => $extraclass);
            }

            $tpl->setarray('FIELD', $extrafields);
            $tpl->setcondition('EXTRAFIELDS', 1);
            return 1;
        }

        return 0;
    }

    public function add($defaultfields, $defaultsafter = array(), $defaultvalues = array())
    {
        global $mysql;
        
        $return = array('total' => 0, 'errnum' => 0, 'errmsg' => 0, 'id' => 0);

        $section = $this->tablename();

        $extrafields = $this->extrafields();

        $fields = array_merge($defaultfields, $extrafields);

        $values = array();

        foreach ($fields as $index => $field) {
            $list = array();
            
            if (is_array($field) && function_exists($field[1][0])) { 
                $result = $field[1][0]($_REQUEST[$field[0]], $field);
                $val = '';
                if ($result['result']) {
                    $val = $result['value'];
                    $fields[$index] = $fields[$index][0];
                } else {
                    array_push($list, $result['message']);
                }
            } else {
                $val = @$_REQUEST[$field];
            }
            
            $values[] = $val;
        }

        $fields = array_merge($fields, $defaultsafter);

        $values = array_merge($values, $defaultvalues);

        if (empty($list)) {
            $mysql->reset()
                ->insert($section)
                ->fields($fields)
                ->values($values)
                ->exec();
            
            $return = array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'errmsg' => array($mysql->errmsg), 'id' => $mysql->lastinserted());
        } else {
            $return['errmsg'] = $list;
        }

        return $return;
    }

    public function edit($defaultfields, $defaultsafter = array(), $defaultvalues = array())
    {
        global $mysql, $site, $api;

        $pid = isset($api) ? $api->pid : $site->arg(2);

        $section = $this->tablename();

        $extrafields = $this->extrafields();

        $values = array();

        $allfields = array_merge($defaultfields, $extrafields);

        foreach ($allfields as $field) {
            $values[] = $field == 'password' ? md5(@$_REQUEST[$field]) : @$_REQUEST[$field];
        }

        foreach ($defaultsafter as $index => $field) {
            $values[] = $field == 'password' ? md5($_REQUEST[$defaultvalues[$index]]) : $defaultvalues[$index];
        }

        $values[] = $pid;

        $mysql->reset()
            ->update($section)
            ->fields(array_merge($allfields, $defaultsafter))
            ->where(sprintf('%sid = ?', substr($section, 0, 1)))
            ->values($values)
            ->exec();

        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $pid);
    }

    public function remove()
    {
        global $mysql, $site;

        $section = $this->tablename();

        $mysql->reset()
            ->delete()
            ->from($section)
            ->where(sprintf('%sid = ?', substr($section, 0, 1)))
            ->values($site->arg(2))
            ->exec();

        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
    }

    private function tablename()
    {
        $class = str_replace('GSD\\', '', get_class($this));

        return substr($class, 0, 8) === 'Extended' ? substr($class, 17) : $class;
    }

    protected function extrafields()
    {
        $section = $this->tablename();

        $_fields = $section.'fields';

        $fields = function_exists($_fields) ? $_fields() : array('list' => array());

        return is_array($fields['list']) ? $fields['list'] : array();
    }
}
