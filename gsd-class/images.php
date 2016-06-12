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

class images extends section implements isection
{
    public function __construct($permission = null)
    {
        global $tpl, $site;
        
        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
        $result = parent::__construct($permission);

        $tpl->setvar('SECTION_TYPE', lang('LANG_IMAGE', 'LOWER'));
        if ($site->a(2) === 'upload') {
            $tpl->repvar('SECTION_ACTION', lang('LANG_NEW_FEMALE'));
        }
        
        $this->labels = array(
            'singular' => 'LANG_IMAGE',
            'plural' => 'LANG_IMAGES'
        );

        return $result;
    }
    
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'images.*, images.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('images')
            ->join('users AS u', 'LEFT')
            ->on('images.creator = u.uid')
            ->where('images.deleted IS NULL');

        if ($options['search']) {
            $mysql->where(sprintf('tags like "%%%s%%"', $options['search']));
        }

        $mysql->order('images.iid');
        $paginator = new paginator($options['numberPerPage'], $options['page'], $this->labels);

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
            'fields' => array_merge(array('iid', 'name', 'description', 'creator', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['ASSET'] = @$item->width ? new image(array('iid' => $item->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $result['list'][$index]['SIZE'] = sprintf('<strong>%s x %s</strong><br>%s', $item->width, $item->height, $item->size);
            }

            $tpl->setarray('IMAGES', $result['list'], 0);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->reset()
            ->select('images.*, images.created')
            ->from('images')
            ->join('users AS u')
            ->on('images.creator = u.uid')
            ->where('iid = ?')
            ->values($id);

        return parent::getcurrent();
    }
    
    protected function fields($update = false)
    {
        $fields = array();
        
        if ($update) {
            $fields[] = new field(array('name' => 'asset', 'label' => lang('LANG_IMAGE'), 'type' => 'file', 'validator' => array('isFile')));
        } else {
            $fields[] = new field(array('name' => 'asset', 'label' => lang('LANG_IMAGE'), 'type' => 'file', 'validator' => array('isFile', 'isRequired')));
            $fields[] = new field(array('name' => 'creator', 'validator' => array('isNumber'), 'notRender' => true));
        }
        $fields[] = new field(array('name' => 'name', 'label' => lang('LANG_NAME'), 'validator' => array('isRequired', 'isString')));
        $fields[] = new field(array('name' => 'description', 'label' => lang('LANG_DESCRIPTION'), 'validator' => array('isString')));
        $fields[] = new field(array('name' => 'tags', 'label' => lang('LANG_TAGS'), 'validator' => array('isString')));
        $fields[] = new field(array('name' => 'extension', 'validator' => array('isRequired', 'isString'), 'notRender' => true));
        $fields[] = new field(array('name' => 'width', 'validator' => array('isRequired', 'isNumber'), 'notRender' => true));
        $fields[] = new field(array('name' => 'height', 'validator' => array('isRequired', 'isNumber'), 'notRender' => true));
        $fields[] = new field(array('name' => 'size', 'validator' => array('isRequired', 'isString'), 'notRender' => true));
        
        return array_merge(parent::fields($update), $fields);
    }
}
