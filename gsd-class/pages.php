<?php

class pages extends section implements isection {

    public function __construct ($id = null) {

        return 0; 
    }

    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;

        $mysql->statement('SELECT pages.*, pages.creator AS creator_id, u.name AS creator_name 
        FROM pages 
        LEFT JOIN users AS u ON pages.creator = u.uid 
        ORDER BY pages.pid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('PAGES_EXIST', $mysql->total > 0);

        if ($mysql->total) {

            foreach ($mysql->result() as $item) {
                $fields = array();
                foreach ($item as $field => $value) {
                    if (is_numeric($field)) {
                        continue;
                    }
                    $fields[strtoupper($field)] = $value;
                }
                $created = explode(' ', $item['created']);
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

                $list[] = $fields;
            }
            $tpl->setarray('PAGES', $list);
            $pages = pageGenerator('FROM pages LEFT JOIN users AS u ON pages.creator = u.uid ORDER BY pages.pid;');

            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);

            parent::generatepaginator($pages);
        }
    }

    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT pages.*, pages.created, u.name AS creator FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.pid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();

            $this->item = $item;
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_PAGE_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_PAGE_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

            $fields['MENU_CHECKED'] = @$item['show_menu'] ? 'checked="checked"' : '';
            $fields['AUTH_CHECKED'] = @$item['require_auth'] ? 'checked="checked"' : '';
            $fields['PUBLISHED_CHECKED'] = @$item['published'] ? 'checked="checked"' : '';

            $image = new image(array(
                'iid' => @$item['og_image'],
                'height' => '100',
                'width' => 'auto',
                'class' => sprintf('preview %s', $item['og_image'] ? '' : 'is-hidden')
            ));

            $partial = new tpl();
            $partial->setvars(array(
                'LABEL' => 'Imagem',
                'NAME' => 'og_image',
                'VALUE' => $item['og_image'] ? $item['og_image'] : 0,
                'IMAGE' => $image,
                'EMPTY' => $item['og_image'] ? 'is-hidden' : ''
            ));
            $partial->setfile('_image');

            $fields['CURRENT_PAGE_OG_IMAGE'] = $partial;

            $tpl->setvars($fields);

        }
    }
    public function generatefields ($section) {
        global $tpl, $mysql;

        parent::generatefields ($section);

        $extrafields = array();

        if (!empty($this->item)) {
            $mysql->statement('SELECT *, mt.file, ls.name AS lsname, smt.file AS sfile
            FROM pagemodules AS pm
LEFT JOIN layoutsections AS ls ON ls.lsid = pm.lsid
LEFT JOIN layoutsectionmoduletypes AS lsmt ON ls.lsid = lsmt.lsid
LEFT JOIN moduletypes AS mt ON mt.mtid = lsmt.mtid
LEFT JOIN moduletypes AS smt ON smt.mtid = lsmt.smtid
WHERE pid = ? ORDER BY pm.pmid DESC', array($this->item['pid']));
            foreach ($mysql->result() as $item) {

                $item['data'] = unserialize($item['data']);
                $extra = array();
                if ($item['file'] == '_image') {
                    $image = new image(array(
                        'iid' => @$item['data'][0][0]['value'],
                        'height' => '100',
                        'width' => 'auto',
                        'class' => sprintf('preview %s', @$item['data'][0][0]['value'] ? '' : 'is-hidden')
                    ));

                    $extra = array(
                        'IMAGE' => $image,
                        'EMPTY' => @$item['data'][0][0]['value'] ? 'is-hidden' : ''
                    );
                }

                $partial = new tpl();
                $partial->setvars(array_merge(array(
                    'LABEL' => ucwords(strtolower($item['lsname'])),
                    'NAME' => 'value_pm_' . $item['pmid'],
                    'VALUE' => @$item['data'][0][0]['value'],
                    'CLASS' => @$item['data'][0][0]['class'],
                    'STYLE' => @$item['data'][0][0]['style']
                ), $extra));

                if ($item['sfile']) {
                    $list = array();
                    foreach ($item['data'] as $index => $data1) {

                        $spartials = '';
                        if (gettype($data1) === 'array') {
                            foreach ($data1 as $data2) {
                                $extra = array();
                                $data = $data2;

                                if ($item['sfile'] == '_image') {
                                    $image = new image(array(
                                        'iid' => @$data['value'],
                                        'height' => '100',
                                        'width' => 'auto',
                                        'class' => sprintf('preview %s', $data['value'] ? '' : 'is-hidden')
                                    ));

                                    $extra = array(
                                        'IMAGE' => $image,
                                        'VALUE' => $data['value'] ? $data['value'] : 0,
                                        'EMPTY' => $data['value'] ? 'is-hidden' : ''
                                    );
                                }
                                $spartial = new tpl();

                                $spartial->setvars(array_merge(array(
                                    'NAME' => 'value_pm_s_' . $index . '_' . $item['pmid'] . '[]',
                                    'VALUE' => $data['value'],
                                    'CLASS' => $data['class'],
                                    'STYLE' => $data['style'],
                                    'LABEL' => 'Value'
                                ), $extra));
                                $spartial->setfile($item['sfile']);

                                $spartials .= $spartial;

                            }
                        }
                        $list[] = array(
                            'ITEM' => $spartials,
                            'EXTRACLASS' => $item['sfile'] == '_image' ? 'image' : ''
                        );
                    }
                        
                    $spartial = new tpl();

                    $spartial->setvars(array(
                        'NAME' => 'value_pm_' . ($index + 1) . '_s' . $item['pmid'] . '[]',
                        'EMPTY' => '',
                        'IMAGE' => new image(array (
                            'height' => '100',
                            'width' => '100',
                            'class' => 'preview is-hidden'
                        ))
                    ));
                    $spartial->setfile($item['sfile']);

                    $partial->setarray('LIST', $list);
                }

                $partial->setfile($item['file']);

                $extrafields[] = array(
                    'FIELD' => $partial,
                    'EXTRACLASS' => 'image'
                );
            }
            $tpl->setarray('FIELD', $extrafields, true);
        }
    }
}
