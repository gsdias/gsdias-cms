<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class documents extends section implements isection
{
    public function __construct($permission = null)
    {
        global $tpl, $site;

        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
        $result = parent::__construct($permission);

        $tpl->setvar('SECTION_TYPE', lang('LANG_DOCUMENT', 'LOWER'));
        if ($site->arg(2) === 'upload') {
            $tpl->repvar('SECTION_ACTION', lang('LANG_NEW_MALE'));
        }

        return $result;
    }

    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'documents.*, documents.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('documents')
            ->join('users AS u', 'LEFT')
            ->on('documents.creator = u.uid')
            ->where('documents.deleted IS NULL');

        if ($options['search']) {
            $mysql->where(sprintf('AND tags like "%%%s%%"', $options['search']));
        }

        $mysql->order('documents.did');
        $paginator = new paginator($options['numberPerPage'], $options['page']);

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
            'fields' => array_merge(array('did', 'name', 'description', 'creator', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['ASSET'] = $item->name;
                $result['list'][$index]['SIZE'] = sprintf('%s', $item->size);
            }

            $tpl->setarray('DOCUMENTS', $result['list'], 0);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->reset()
            ->select('documents.*, documents.created')
            ->from('documents')
            ->join('users AS u')
            ->on('documents.creator = u.uid')
            ->where('did = ?')
            ->values($id);

        return parent::getcurrent();
    }
    
    protected function fields($update = false)
    {
        $fields = array();
        
        $fields[] = new field(array('name' => 'asset', 'label' => lang('LANG_DOCUMENT'), 'type' => 'file', 'validator' => array('isFile')));
        $fields[] = new field(array('name' => 'name', 'label' => lang('LANG_NAME'), 'validator' => array('isRequired', 'isString')));
        $fields[] = new field(array('name' => 'description', 'label' => lang('LANG_DESCRIPTION'), 'validator' => array('isString')));
        $fields[] = new field(array('name' => 'tags', 'label' => lang('LANG_TAGS'), 'validator' => array('isString')));
        $fields[] = new field(array('name' => 'extension', 'validator' => array('isRequired', 'isString'), 'notRender' => true));
        $fields[] = new field(array('name' => 'size', 'validator' => array('isRequired', 'isString'), 'notRender' => true));
        $fields[] = new field(array('name' => 'creator', 'validator' => array('isNumber'), 'notRender' => true));
        
        return array_merge(parent::fields($update), $fields);
    }
}
