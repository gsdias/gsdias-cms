<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class layouts extends section implements isection {

    public function __construct ($id = null) {

        return 0;
    }

    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;

        $mysql->statement('SELECT layouts.*, u.name AS creator_name, u.uid AS creator_id
        FROM layouts
        LEFT JOIN users AS u ON layouts.creator = u.uid
        ORDER BY lid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('LAYOUTS_EXIST', $mysql->total > 0);

        if ($mysql->total) {

            foreach ($mysql->result() as $item) {
                $fields = array();
                foreach ($item as $field => $value) {
                    $fields[strtoupper($field)] = $value;
                }
                $created = explode(' ', $item->created);
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]);

                $list[] = $fields;
            }
            $tpl->setarray('LAYOUTS', $list);
            $pages = pageGenerator('FROM layouts LEFT JOIN users AS u ON layouts.creator = u.uid ORDER BY lid;');

            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);

            $this->generatepaginator($pages);
        }
    }

    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT layouts.*, layouts.created FROM layouts LEFT JOIN users AS u ON layouts.creator = u.uid WHERE layouts.lid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();

            $this->item = $item;
            $created = explode(' ', $item->created);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_LAYOUT_'. strtoupper($field)] = $value;
            }

            $tpl->setvars($fields);

        }
    }
}
