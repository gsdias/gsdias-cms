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

class pages extends section implements isection
{
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('pages AS p')
            ->join('users AS u', 'LEFT')
            ->on('p.creator = u.uid')
            ->join('pages AS pp', 'LEFT')
            ->on('p.parent = pp.pid');

        if (@$_REQUEST['search']) {
            $mysql->where(sprintf('MATCH (p.title, p.description) AGAINST ("%s" WITH QUERY EXPANSION)', $_REQUEST['search']));
        }
        if (@$_REQUEST['filter']) {
            $tpl->setvar('FILTER_'.strtoupper($_REQUEST['filter']), 'selected="selected"');
            switch ($_REQUEST['filter']) {
                case 'published':
                    $mysql->where(sprintf('%s p.published IS NOT NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
                case 'unpublished':
                    $mysql->where(sprintf('%s p.published IS NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
                case 'visiblemenu':
                    $mysql->where(sprintf('%s p.show_menu IS NOT NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
                case 'invisiblemenu':
                    $mysql->where(sprintf('%s p.show_menu IS NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
                case 'secure':
                    $mysql->where(sprintf('%s p.require_auth IS NOT NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
                case 'nonsecure':
                    $mysql->where(sprintf('%s p.require_auth IS NULL', @$_REQUEST['search'] ? 'AND' : ''));
                break;
            }
        }

        $mysql->order('p.index');
        $page = @$_REQUEST['page'] ? $_REQUEST['page'] : 1;
        $paginator = new paginator(@$options['numberPerPage'], $page);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(', %s', $field);
            }
        }

        $limit = $paginator->pageLimit() ? $paginator->pageLimit() - 1 : 0;
        $totalPages = $paginator->getPageTotal();
        $numberPerPage = $totalPages == $page || $page == 1 ? $options['numberPerPage'] + 1 : $options['numberPerPage'] + 2;

        $mysql->select($_fields)
            ->limit($limit, $numberPerPage)
            ->exec();

        $result = parent::getlist(array(
            'search' => $options['search'],
            'results' => $mysql->result(),
            'fields' => array_merge(array('pid', 'title', 'beautify', 'creator', 'creator_name', 'creator_id', 'index'), $fields),
            'paginator' => $paginator,
            'totalPages' => $totalPages
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['UNPUBLISHED'] = $item->published ? '' : sprintf('<br>(%s)', lang('LANG_UNPUBLISHED'));
            }

            $tpl->setarray('PAGES', $result['list']);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->statement('SELECT pages.*, pages.created, u.name AS creator FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.pid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        if (!empty($result['item'])) {
            $item = $result['item'];
            $created = explode(' ', $item->created);
            $fields = $result['fields'];

            $fields['CURRENT_PAGES_CREATED'] = timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]);

            $fields['MENU_CHECKED'] = @$item->show_menu ? 'checked="checked"' : '';
            $fields['AUTH_CHECKED'] = @$item->require_auth ? 'checked="checked"' : '';
            $fields['PUBLISHED_CHECKED'] = @$item->published ? 'checked="checked"' : '';
            $fields['CURRENT_PAGES_STATUS'] = @$item->published ? 'Publicada' : 'Por publicar';

            $image = new image(array(
                'iid' => @$item->og_image,
                'height' => '100',
                'width' => 'auto',
                'class' => sprintf('preview %s', $item->og_image ? '' : 'is-hidden'),
            ));

            $partial = new tpl();
            $partial->setvars(array(
                'LABEL' => 'Imagem',
                'NAME' => 'og_image',
                'VALUE' => $item->og_image,
                'IMAGE' => $image,
                'EMPTY' => $item->og_image ? 'is-hidden' : '',
            ));
            $partial->setfile('_image');

            $fields['CURRENT_PAGES_OG_IMAGE'] = $partial;

            $tpl->repvars($fields);

            $mysql->statement('SELECT * FROM pages_review WHERE pid = ?;', array($id));

            if ($mysql->total) {
                $review = array();
                foreach ($mysql->result() as $field) {
                    $review[] = array(
                        'KEY' => $field->prid,
                        'VALUE' => $field->modified,
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
                    'SELECTED' => $field->pid == $this->item->parent ? 'selected="selected"' : '',
                );
            }
            $tpl->setarray('PARENT', $parent);
        }

        return $result['item'];
    }

    public function generatefields()
    {
        global $tpl, $mysql;

        $hasextra = parent::generatefields();

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
                        'class' => sprintf('preview %s', @$item->data['list'][0][0]['value'] ? '' : 'is-hidden'),
                    ));

                    $extra = array(
                        'IMAGE' => $image,
                        'EMPTY' => @$item->data['list'][0][0]['value'] ? 'is-hidden' : '',
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
                                        'class' => sprintf('preview %s', $data['value'] ? '' : 'is-hidden'),
                                    ));

                                    $extra = array(
                                        'IMAGE' => $image,
                                        'VALUE' => $data['value'] ? $data['value'] : 0,
                                        'EMPTY' => $data['value'] ? 'is-hidden' : '',
                                    );
                                }
                                $spartial = new tpl();

                                $spartial->setvars(array_merge(array(
                                    'NAME' => 'value_pm_s_'.$index.'_'.$item->pmid.'[]',
                                    'VALUE' => $data['value'],
                                    'CLASS' => $data['class'],
                                    'STYLE' => $data['style'],
                                    'LABEL' => 'Value',
                                ), $extra));
                                $spartial->setfile($item->sfile);

                                $spartials .= $spartial;
                            }
                        }
                        $list[] = array(
                            'ITEM' => $spartials,
                            'EXTRACLASS' => $item->sfile == '_image' ? 'image' : '',
                        );
                    }

                    $spartial = new tpl();

                    $spartial->setvars(array(
                        'NAME' => 'value_pm_'.($index + 1).'_s'.$item->pmid.'[]',
                        'EMPTY' => '',
                        'IMAGE' => new image(array(
                            'height' => '100',
                            'width' => '100',
                            'class' => 'preview is-hidden',
                        )),
                    ));
                    $spartial->setfile($item->sfile);

                    $partial->setarray('LIST', $list);
                }

                $partial->setfile($item->file);

                $extrafields[] = array(
                    'FIELD' => $partial,
                    'EXTRACLASS' => 'image',
                );
            }
            $tpl->setarray('FIELD', $extrafields, true);
            $tpl->setcondition('EXTRAFIELDS', $hasextra || !empty($extrafields));
        }
    }

    public function add($fields = array())
    {
        global $mysql, $site, $user;

        $mysql->reset()
            ->select('max(`index`) AS max')
            ->from('pages')
            ->exec();

        $index = @$mysql->singleresult();

        $_REQUEST['creator'] = $user->id;
        $_REQUEST['index'] = ($index != null ? $index + 1 : 0);
        $_REQUEST['created'] = date('Y-m-d H:i:s', time());

        $result = parent::add($fields);
        
        if (empty($result['errmsg'][0])) {
            $this->update_beautify($result['id']);
        }

        return $result;
    }

    public function edit($defaultfields = array())
    {
        global $mysql, $site, $api;

        $pid = isset($api) ? $api->pid : $site->arg(2);
        $mysql->reset()
            ->select()
            ->from('pages')
            ->where('pid = ?')
            ->values($pid)
            ->exec();

        $currentpage = $mysql->singleline();
        $hasChanged = 0;
        $fields = array();

        foreach ($defaultfields as $field) {
            $fieldname = is_array($field) ? $field[0] : $field;
            if ($currentpage->{$fieldname} != @$_REQUEST[$fieldname]) {
                $hasChanged = 1;
            }
            array_push($fields, $currentpage->{$fieldname});
        }

        $result = parent::edit($defaultfields);

        $this->update_beautify($pid);

        if ($hasChanged) {
            array_push($fields, $currentpage->modified);
            array_push($defaultfields, 'modified');
            $this->page_review($defaultfields, $fields);
        }

        return $result;
    }

    private function update_beautify($pid)
    {
        global $mysql;

        $mysql->statement('SELECT pp.beautify, p.url
        FROM pages AS p
        LEFT JOIN pages AS pp ON p.parent = pp.pid
        WHERE p.pid = ?;', array($pid));

        $result = $mysql->singleline();

        $mysql->reset()
            ->update('pages')
            ->fields(array('beautify'))
            ->where('pid = ?')
            ->values(array(sprintf('%s%s', $result->beautify, $result->url), $pid))
            ->exec();
    }

    private function page_review($defaultfields = array(), $fields = array())
    {
        global $mysql, $user, $site;

        array_push($fields, $user->id);
        array_push($fields, $site->arg(2));
        $questions = str_repeat(', ? ', sizeof($fields));
        $mysql->statement(sprintf('INSERT INTO pages_review (%s, creator, pid) values (%s);', implode(',', $this->getfieldlist($defaultfields)), substr($questions, 2)), $fields);
    }

    private function getfieldlist($defaultfields)
    {
        $list = array();
        foreach($defaultfields as $field) {
            $list[] = is_array($field) ? $field[0] : $field;
        }

        return $list;
    }

    private function partialtpl($item, $lsname, $pmid, $extra)
    {
        $partial = new tpl();
        $partial->setvars(array_merge(array(
            'LABEL' => lang(sprintf('LANG_%s', $lsname)),
            'NAME' => 'value_pm_'.$pmid,
            'VALUE' => @$item['value'],
            'CLASS' => @$item['class'],
            'STYLE' => @$item['style'],
        ), $extra));

        return $partial;
    }
}
