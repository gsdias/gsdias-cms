<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class documents extends section implements isection
{
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'documents.*, documents.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('documents')
            ->join('users AS u', 'LEFT')
            ->on('documents.creator = u.uid');

        if ($options['search']) {
            $mysql->where(sprintf('tags like "%%%s%%"', $options['search']));
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

            $tpl->setarray('DOCUMENTS', $result['list']);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->statement('SELECT documents.*, documents.created
        FROM documents
        LEFT JOIN users AS u ON documents.creator = u.uid
        WHERE documents.did = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }
}
