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

class layouts extends section implements isection
{
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'layouts.*, u.name AS creator_name, u.uid AS creator_id';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('layouts')
            ->join('users AS u', 'LEFT')
            ->on('layouts.creator = u.uid');

        if ($options['search']) {
            $mysql->where(sprintf('layouts.name like "%%%s%%"', $options['search']));
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
}
