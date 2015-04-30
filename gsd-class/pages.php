<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class pages extends section implements isection {

    public function __construct ($id = null) {
        return 0; 
    }

    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;

        $mysql->statement('SELECT p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name
        FROM pages AS p
        LEFT JOIN users AS u ON p.creator = u.uid
        LEFT JOIN pages AS pp ON p.parent = pp.pid
        ORDER BY p.pid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('PAGES_EXIST', $mysql->total > 0);

        if ($mysql->total) {

            foreach ($mysql->result() as $item) {
                $fields = array();
                foreach ($item as $field => $value) {
                    $fields[strtoupper($field)] = $value;
                }
                $created = explode(' ', $item->created);
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())), $created[1]);

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
            $created = explode(' ', $item->created);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_PAGE_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_PAGE_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())), $created[1]);

            $fields['MENU_CHECKED'] = @$item->show_menu ? 'checked="checked"' : '';
            $fields['AUTH_CHECKED'] = @$item->require_auth ? 'checked="checked"' : '';
            $fields['PUBLISHED_CHECKED'] = @$item->published ? 'checked="checked"' : '';
            $fields['CURRENT_PAGE_STATUS'] = @$item->published ? 'Publicada' : 'Por publicar';

            $image = new image(array(
                'iid' => @$item->og_image,
                'height' => '100',
                'width' => 'auto',
                'class' => sprintf('preview %s', $item->og_image ? '' : 'is-hidden')
            ));

            $partial = new tpl();
            $partial->setvars(array(
                'LABEL' => 'Imagem',
                'NAME' => 'og_image',
                'VALUE' => $item->og_image ? $item->og_image : 0,
                'IMAGE' => $image,
                'EMPTY' => $item->og_image ? 'is-hidden' : ''
            ));
            $partial->setfile('_image');

            $fields['CURRENT_PAGE_OG_IMAGE'] = $partial;

            $tpl->setvars($fields);

            $mysql->statement('SELECT * FROM pages_review WHERE pid = ?;', array($id));

            if ($mysql->total) {
                $review = array();
                foreach ($mysql->result() as $field) {
                    $review[] = array(
                        'KEY' => $field->prid,
                        'VALUE' => $field->modified
                    );
                }
                $tpl->setarray('VERSION', $review);
            }

            $mysql->statement('SELECT p.pid, p.title
                FROM pages AS p
                WHERE pid <> ?;', array($this->item->pid));

            $parent = array();
            foreach ($mysql->result() as $field) {

                $parent[] = array(
                    'KEY' => $field->pid,
                    'VALUE' => $field->title,
                    'SELECTED' => $field->pid == $this->item['parent'] ? 'selected="selected"' : ''
                );
            }
            $tpl->setarray('PARENT', $parent);
        }
    }

    public function generatefields ($section, $current) {
        global $tpl, $mysql;

        parent::generatefields ($section);

        $extrafields = array();

        if (!empty($this->item)) {

            $mysql->statement('SELECT *, mt.file, ls.label AS lsname, smt.file AS sfile
            FROM pagemodules AS pm
LEFT JOIN layoutsections AS ls ON ls.lsid = pm.lsid
LEFT JOIN layoutsectionmoduletypes AS lsmt ON ls.lsid = lsmt.lsid
LEFT JOIN moduletypes AS mt ON mt.mtid = lsmt.mtid
LEFT JOIN moduletypes AS smt ON smt.mtid = lsmt.smtid
WHERE pid = ? ORDER BY pm.pmid DESC', array($this->item->pid));

            foreach ($mysql->result() as $item) {

                $item->data = unserialize($item->data);

                $extra = array();
                if ($item->file == '_image') {
                    $image = new image(array(
                        'iid' => @$item->data['list'][0][0]['value'],
                        'height' => '100',
                        'width' => 'auto',
                        'class' => sprintf('preview %s', @$item->data['list'][0][0]['value'] ? '' : 'is-hidden')
                    ));

                    $extra = array(
                        'IMAGE' => $image,
                        'EMPTY' => @$item->data['list'][0][0]['value'] ? 'is-hidden' : ''
                    );
                }

                if ($item->sfile) {
                    $partial = $this->partialtpl($item->data, $item->lsname, $item->pmid, $extra);
                } else {
                    $partial = $this->partialtpl($item->data['list'][0][0], $item->lsname, $item->pmid, $extra);
                }

                if ($item->sfile) {
                    $list = array();
                    foreach ($item->data['list'] as $index => $data1) {

                        $spartials = '';
                        if (gettype($data1) === 'array') {
                            foreach ($data1 as $data2) {
                                $extra = array();
                                $data = $data2;

                                if ($item->sfile == '_image') {
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
                                    'NAME' => 'value_pm_s_' . $index . '_' . $item->pmid . '[]',
                                    'VALUE' => $data['value'],
                                    'CLASS' => $data['class'],
                                    'STYLE' => $data['style'],
                                    'LABEL' => 'Value'
                                ), $extra));
                                $spartial->setfile($item->sfile);

                                $spartials .= $spartial;

                            }
                        }
                        $list[] = array(
                            'ITEM' => $spartials,
                            'EXTRACLASS' => $item->sfile == '_image' ? 'image' : ''
                        );
                    }
                        
                    $spartial = new tpl();

                    $spartial->setvars(array(
                        'NAME' => 'value_pm_' . ($index + 1) . '_s' . $item->pmid . '[]',
                        'EMPTY' => '',
                        'IMAGE' => new image(array (
                            'height' => '100',
                            'width' => '100',
                            'class' => 'preview is-hidden'
                        ))
                    ));
                    $spartial->setfile($item->sfile);

                    $partial->setarray('LIST', $list);
                }

                $partial->setfile($item->file);

                $extrafields[] = array(
                    'FIELD' => $partial,
                    'EXTRACLASS' => 'image'
                );
            }
            $tpl->setarray('FIELD', $extrafields, true);
        }
        $tpl->setcondition('EXTRAFIELDS', !empty($extrafields));
    }

    public function add ($defaultfields, $defaultsafter = array(), $defaultvalues = array()) {
        global $mysql, $site;

        $result = parent::add($defaultfields, $defaultsafter, $defaultvalues);

        $this->update_beautify($result['id']);

        return $result;
    }

    public function edit ($defaultfields = array()) {
        global $mysql, $site;

        $mysql->statement('SELECT * FROM pages WHERE pid = ?;', array($site->arg(2)));
        $currentpage = $mysql->singleline();
        $hasChanged = 0;
        $fields = array();

        foreach ($defaultfields as $field) {
            if ($currentpage->{$field} != $_REQUEST[$field]) {
                $hasChanged = 1;
            }
            array_push($fields, $currentpage->{$field});
        }

        $result = parent::edit($defaultfields);

        $this->update_beautify($site->arg(2));

        if ($hasChanged) {
            array_push($fields, $currentpage->modified);
            array_push($defaultfields, 'modified');
            $this->page_review($defaultfields, $fields);
        }

        return $result;
    }

    private function update_beautify ($pid) {
        global $mysql;

        $mysql->statement('SELECT pp.beautify, p.url FROM pages AS p LEFT JOIN pages AS pp ON p.parent = pp.pid WHERE p.pid = ?;', array($pid));
        $result = $mysql->singleline();
        $mysql->statement('UPDATE pages SET beautify = ? WHERE pid = ?;', array(sprintf('%s%s', $result->beautify, $result->url), $pid));
    }

    private function page_review ($defaultfields = array(), $fields = array()) {
        global $mysql, $user, $site;

        array_push($fields, $user->id);
        array_push($fields, $site->arg(2));
        $questions = str_repeat(", ? ", sizeof($fields));
        $mysql->statement(sprintf('INSERT INTO pages_review (%s, creator, pid) values (%s);', implode(',', $defaultfields), substr($questions, 2)), $fields);
    }

    private function partialtpl ($item, $lsname, $pmid, $extra) {
        $partial = new tpl();
        $partial->setvars(array_merge(array(
            'LABEL' => lang(sprintf('LANG_%s', $lsname)),
            'NAME' => 'value_pm_' . $pmid,
            'VALUE' => @$item['value'],
            'CLASS' => @$item['class'],
            'STYLE' => @$item['style']
        ), $extra));

        return $partial;
    }
}
