<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class layouts extends section implements isection
{
    public function __construct($permission = NULL)
    {
        global $tpl, $site;
        
        $tpl->setvar('SECTION_TYPE', lang('LANG_LAYOUT', 'LOWER'));
        if ($site->arg(3) === 'edit') {
            $tpl->setvar('SECTION_ACTION', lang('LANG_EDIT'));
        } else {
            $tpl->setvar('SECTION_ACTION', lang('LANG_NEW_MALE'));
        }
        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN;
        return parent::__construct($permission);
    }
    
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'l.lid, l.name, l.created, u.name AS creator_name, u.uid AS creator_id';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('layouts AS l')
            ->join('users AS u', 'LEFT')
            ->on('l.creator = u.uid');

        if ($options['search']) {
            $mysql->where(sprintf('l.name like "%%%s%%"', $options['search']));
        }

        $mysql->order('lid');
        $paginator = new paginator(@$options['numberPerPage'], @$_REQUEST['page']);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(', %s', $field);
            }
        }

        $mysql->select($_fields)
            ->limit($paginator->pageLimit(), $options['numberPerPage'])
            ->exec();

        $result = parent::getlist(array(
            'search' => $options['search'],
            'results' => $mysql->result(),
            'fields' => array_merge(array('lid', 'name', 'creator', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->reset()
            ->select()
            ->from('layouts')
            ->where('lid = ?')
            ->values($id)
            ->exec();

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }

    public function add()
    {
        return parent::add();
    }
    
    private function getTypes()
    {
        global $mysql;
        
        $mysql->reset()
            ->select()
            ->from('layouttypes')
            ->exec();

        $types = array(0 => lang('LANG_CHOOSE'));
        foreach ($mysql->result() as $item) {
            $types[$item->ltid] = $item->name;
        }

        return $types;
    }
    
    private function getFiles()
    {        
        $templatefiles = scandir(CLIENTTPLPATH.'_layouts');

        $templates = array(0 => lang('LANG_CHOOSE'));

        foreach ($templatefiles as $file) {
            if ($file != '.' && $file != '..') {
                $templates[$file] = $file;
            }
        }

        return $templates;
    }
    
    protected function fields($update = false)
    {
        
        $fields = array();
        
        $fields[] = new field(array('name' => 'name', 'label' => lang('LANG_NAME'), 'validator' => array('isRequired', 'isString')));
        $fields[] = new field(array('name' => 'file', 'label' => lang('LANG_TEMPLATE'), 'validator' => array('isRequired', 'isString'), 'type' => 'select', 'values' => $this->getFiles()));
        $fields[] = new field(array('name' => 'ltid', 'label' => lang('LANG_TYPE'), 'validator' => array('isRequired', 'isNumber'), 'type' => 'select', 'values' => $this->getTypes()));
        $fields[] = new field(array('name' => 'creator', 'validator' => array('isNumber'), 'notRender' => true));
        
        return array_merge(parent::fields($update), $fields);
    }
}
