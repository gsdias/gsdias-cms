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
namespace GSD;

class images extends section implements isection
{
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'images.*, images.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('images')
            ->join('users AS u', 'LEFT')
            ->on('images.creator = u.uid');

        if ($options['search']) {
            $mysql->where(sprintf('tags like "%%%s%%"', $options['search']));
        }

        $mysql->order('images.iid');
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
            'fields' => array_merge(array('iid', 'name', 'description', 'creator', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['ASSET'] = @$item->width ? new image(array('iid' => $item->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $result['list'][$index]['SIZE'] = sprintf('<strong>%s x %s</strong><br>%s', $item->width, $item->height, $item->size);
            }

            $tpl->setarray('IMAGES', $result['list']);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->statement('SELECT images.*, images.created
        FROM images
        LEFT JOIN users AS u ON images.creator = u.uid
        WHERE images.iid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }
}
