<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

abstract class section implements isection
{
    public $item = array();

    public function __construct($id = 0)
    {
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
            $tpl->setcondition('EXTRAFIELDS');
        }
    }

    public function add($defaultfields, $defaultsafter = array(), $defaultvalues = array())
    {
        global $mysql;

        $section = $this->tablename();

        $extrafields = $this->extrafields();

        $fields = array_merge($defaultfields, $extrafields);

        $values = array();

        foreach ($fields as $field) {
            $values[] = $_REQUEST[$field];
        }

        $fields = array_merge($fields, $defaultsafter);

        $values = array_merge($values, $defaultvalues);

        $mysql->reset()
            ->insert($section)
            ->fields($fields)
            ->values($values)
            ->exec();

        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'errmsg' => $mysql->errmsg, 'id' => $mysql->lastinserted());
    }

    public function edit($defaultfields, $defaultsafter = array(), $defaultvalues = array())
    {
        global $mysql, $site;

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

        $values[] = $site->arg(2);

        $mysql->reset()
            ->update($section)
            ->fields(array_merge($allfields, $defaultsafter))
            ->where(sprintf('%sid = ?', substr($section, 0, 1)))
            ->values($values)
            ->exec();

        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
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
