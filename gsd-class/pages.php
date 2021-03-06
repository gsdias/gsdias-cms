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

class pages extends section implements isection
{
    public function __construct($permission = null)
    {
        global $tpl, $site;
        
        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
        $result = parent::__construct($permission);

        $tpl->setvar('SECTION_TYPE', lang('LANG_PAGE', 'LOWER'));
        if ($site->a(2) === 'create') {
            $tpl->repvar('SECTION_ACTION', lang('LANG_NEW_FEMALE'));
        }
        
        $this->labels = array(
            'singular' => 'LANG_PAGE',
            'plural' => 'LANG_PAGES'
        );

        return $result;
    }
    
    public function getlist($options)
    {
        global $mysql, $tpl, $site;

        $_fields = 'p.*, p.creator AS creator_id, u.name AS creator_name, if(p.beautify like concat(if(pp.beautify IS NULL , "", pp.beautify), p.url), 0, 1) AS sync, l.name AS layout';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('pages AS p')
            ->join('users AS u', 'LEFT')
            ->on('p.creator = u.uid')
            ->join('pages AS pp', 'LEFT')
            ->on('p.parent = pp.pid')
            ->join('layouts AS l', 'LEFT')
            ->on('l.lid = p.lid')
            ->where('p.deleted IS NULL');

        if ($site->p('search')) {
            $mysql->where(sprintf('MATCH (p.title, p.description) AGAINST ("%s" WITH QUERY EXPANSION)', $site->p('search')));
        }
        if ($site->p('filter')) {
            $tpl->setvar('FILTER_'.strtoupper($site->p('filter')), 'selected="selected"');
            switch ($site->p('filter')) {
                case 'published':
                    $mysql->where('p.published IS NOT NULL');
                break;
                case 'unpublished':
                    $mysql->where('p.published IS NULL');
                break;
                case 'visiblemenu':
                    $mysql->where('p.show_menu IS NOT NULL');
                break;
                case 'invisiblemenu':
                    $mysql->where('p.show_menu IS NULL');
                break;
                case 'secure':
                    $mysql->where('p.require_auth IS NOT NULL');
                break;
                case 'nonsecure':
                    $mysql->where('p.require_auth IS NULL');
                break;
            }
        }

        $mysql->order('p.index');
        $page = $site->p('page') ? $site->p('page') : 1;
        $paginator = new paginator(@$options['numberPerPage'], $page, $this->labels);

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
            'fields' => array_merge(array('pid', 'title', 'beautify', 'creator', 'creator_name', 'creator_id', 'index', 'sync', 'layout'), $fields),
            'paginator' => $paginator,
            'totalPages' => $totalPages
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['UNPUBLISHED'] = $item->published ? '' : sprintf('<br>(%s)', lang('LANG_UNPUBLISHED'));
                $result['list'][$index]['SYNC'] = $item->sync ? sprintf('%s (<a href="/admin/pages/%d/sync" class="redLabel">%s</a>)', ($item->published ? '<br>': ''), $item->pid, 'Sync') : '';
            }

            $tpl->setarray('PAGES', $result['list'], 0);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $mysql, $tpl;

        $mysql->reset()
            ->select('pages.*, pages.created, u.name AS creator, l.file AS layout')
            ->from('pages')
            ->join('users AS u')
            ->on('pages.creator = u.uid')
            ->join('layouts AS l')
            ->on('pages.lid = l.lid')
            ->where('pid = ?')
            ->values($id);

        $result = parent::getcurrent();

        if (!empty($this->item)) {
            $item = $this->item;
            $created = explode(' ', $item->created);

            $result['CURRENT_PAGES_CREATED'] = timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]);
            $result['CURRENT_PAGES_STATUS'] = @$item->published ? 'Publicada' : 'Por publicar';

            $tpl->repvars($result);

            $mysql->statement('SELECT * FROM pages_extra WHERE pid = ?;', array($id));
            if ($mysql->total) {
                foreach ($mysql->result() as $field) {
                    $this->item->{$field->name} = $field->value;
                }
            }
        }

        return $result;
    }

    public function generatefields($initial = false)
    {
        global $tpl, $mysql;

        $hasextra = parent::generatefields($initial);

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

    public function add()
    {
        global $mysql, $site, $user;
        
        $path = explode('/', $site->p('url'));
        
        if (@$path[1] === 'p') {
            return array(
                'errnum' => 0,
                'errmsg' => array('Reserved path for native url'),
                'total' => 0
            );
        }

        $mysql->reset()
            ->delete()
            ->from('pages')
            ->where('url = ? AND parent = ? AND deleted IS NOT NULL')
            ->values(array($site->p('url'), $site->p('parent')))
            ->exec();

        $mysql->reset()
            ->select('max(`index`) AS max')
            ->from('pages')
            ->exec();

        $index = @$mysql->singleresult();

        $_REQUEST['index'] = ($index != null ? $index + 1 : 0);
        $_REQUEST['url'] = $this->buildurl($site->p('title'));

        $result = parent::add();
        
        if (empty($result['errmsg'][0])) {
            $this->update_beautify($result['id']);
        }

        $fields = $this->fields();

        foreach ($fields as $field) {
            if ($field->getExtra()) {
                $mysql->reset()
                ->insert('pages_extra')
                ->fields(array('pid', 'name', 'value'))
                ->values(array($result['id'], $field->getName(), $site->p($field->getName())))
                ->exec();
            }
        }

        return $result;
    }

    private function buildurl($title)
    {
        $patterns = array('/ç/', '/á/', '/à/', '/â/', '/ã/',
                          '/è/', '/é/', '/ê/',
                          '/í/', '/ì/', '/î/',
                          '/ò/', '/ó/', '/ô/', '/õ/',
                          '/ù/', '/ú/', '/û/');
        $replacements = array('c', 'a', 'a', 'a', 'a',
                              'e', 'e', 'e',
                              'i', 'i', 'i',
                              'o', 'o', 'o', 'o',
                              'u', 'u', 'u');

        $url = preg_replace($patterns, $replacements, mb_strtolower(trim($title), 'UTF-8'));
        $url = preg_replace('#[^A-Za-z0-9\- ]+#', '', $url);
        $url = preg_replace('/\ /', '-', $url);

        return '/'.$url;
    }

    public function settings()
    {

    }
    
    private function getLayouts()
    {
        global $mysql;
        
        $mysql->reset()
            ->select('lid, name')
            ->from('layouts')
            ->where('deleted IS NULL')
            ->exec();

        $types = array(0 => lang('LANG_CHOOSE'));
        foreach ($mysql->result() as $item) {
            $types[$item->lid] = $item->name;
        }

        return $types;
    }
    
    private function getParents($id = '')
    {
        global $mysql;
        
        $mysql->reset()
            ->select('pid, title')
            ->from('pages')
            ->where('pid <> ? AND deleted IS NULL')
            ->values($id)
            ->exec();

        $types = array(0 => lang('LANG_CHOOSE'));
        foreach ($mysql->result() as $item) {
            $types[$item->pid] = $item->title;
        }

        return $types;
    }

    public function edit()
    {
        global $mysql, $site, $api;
        
        $pid = isset($api) ? $api->pid : $site->a(2);
        $mysql->reset()
            ->select()
            ->from('pages')
            ->where('pid = ?')
            ->values($pid)
            ->exec();

        $currentpage = $mysql->singleline();
        $hasChanged = 0;
        $fieldsvalue = array();

        $defaultfields = $this->fields(true);

        foreach ($defaultfields as $index => $field) {
            $fieldname = $field->getName();
            $defaultfields[$index] = $fieldname;
            if (!$field->getExtra()) {
                if ($currentpage->{$fieldname} !== $site->p($fieldname)) {
                    $hasChanged = 1;
                }
                array_push($fieldsvalue, $currentpage->{$fieldname});
            } else {
                unset($defaultfields[$index]);
            }
        }

        $result = parent::edit();

        $fields = $this->fields(true);

        $list = array();

        foreach ($fields as $field) {
            if ($field->getExtra()) {
                $fieldinfo = $this->filterField($field);

                if (!$fieldinfo['result']) {
                    $list[] = $fieldinfo['message'];
                    continue;
                }

                $mysql->reset()
                    ->update('pages_extra')
                    ->fields(array('value'))
                    ->where('pid = ? AND name = ?')
                    ->values(array($site->p($field->getName()), $pid, $field->getName()))
                    ->exec();

                if (!$mysql->total) {
                    $mysql->reset()
                        ->insert('pages_extra', 'IGNORE')
                        ->fields(array('pid', 'name', 'value'))
                        ->values(array($pid, $field->getName(), $site->p($field->getName())))
                        ->exec();
                }
            }
        }

        if (!empty($list)) {
            $result['errmsg'] = $list;
            $this->result['errmsg'] = $list;
        }

        if ($currentpage->parent !== $site->p('parent')) {
            $this->update_beautify($pid);
        }

        if (empty($result['errmsg']) && $hasChanged) {
            array_push($fieldsvalue, $currentpage->modified);
            array_push($defaultfields, 'modified');
            $this->page_review($defaultfields, $fieldsvalue);
        }

        return $result;
    }

    private function update_beautify($pid)
    {
        global $mysql;

        $mysql->reset()
            ->select('pp.beautify, p.url')
            ->from('pages AS p')
            ->join('pages AS pp', 'LEFT')
            ->on('p.parent = pp.pid')
            ->where('p.pid = ?')
            ->values(array($pid))
            ->exec();

        $result = $mysql->singleline();

        $mysql->reset()
            ->update('pages')
            ->fields(array('beautify'))
            ->where('pid = ?')
            ->values(array(sprintf('%s%s', $result->beautify, $result->url), $pid))
            ->exec();
    }

    private function page_review($fields = array(), $values = array())
    {
        global $mysql, $user, $site;

        array_push($values, $user->id);
        array_push($values, $site->a(2));

        $mysql->reset()
            ->insert('pages_review')
            ->fields(array_merge($this->getfieldlist($fields), array('creator', 'pid')))
            ->values($values)
            ->exec();
    }

    private function getfieldlist($fields)
    {
        $list = array();
        foreach ($fields as $field) {
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
        ), $extra));

        return $partial;
    }
    
    protected function fields($update = false)
    {
        $fields = array();
        $id = is_array($this->item) ? '' : $this->item->pid;
        
        $fields[] = new field(array('name' => 'title', 'validator' => array('isRequired', 'isString'), 'autofocus' => 1, 'label' => lang('LANG_TITLE')));
        if (!$update) {
            $fields[] = new field(array('name' => 'url', 'validator' => array('isRequired', 'isString'), 'label' => lang('LANG_URL'), 'notRender' => true));
            $fields[] = new field(array('name' => 'lid', 'type' => 'select', 'validator' => array('isRequired', 'isNumber'), 'label' => lang('LANG_LAYOUT'), 'values' => $this->getLayouts()));
        }
        $fields[] = new field(array('name' => 'description', 'type' => 'textarea', 'validator' => array('isString'), 'label' => lang('LANG_DESCRIPTION')));
        $fields[] = new field(array('name' => 'keywords', 'validator' => array('isString'), 'label' => lang('LANG_KEYWORDS')));
        $fields[] = new field(array('name' => 'tags', 'validator' => array('isString'), 'label' => lang('LANG_TAGS')));
        $fields[] = new field(array('name' => 'og_title', 'validator' => array('isString'), 'label' => lang('LANG_OG_TITLE')));
        $fields[] = new field(array('name' => 'og_image', 'type' => 'image', 'validator' => array('isNumber'), 'label' => lang('LANG_OG_IMAGE')));
        $fields[] = new field(array('name' => 'og_description', 'type' => 'textarea', 'validator' => array('isString'), 'label' => lang('LANG_OG_DESCRIPTION')));
        $fields[] = new field(array('name' => 'parent', 'type' => 'select', 'validator' => array('isNumber'), 'label' => lang('LANG_PARENT'), 'values' => $this->getParents($id)));
        $fields[] = new field(array('name' => 'show_menu', 'validator' => array('isCheckbox'), 'label' => lang('LANG_SHOW_MENU'), 'type' => 'checkbox'));
        $fields[] = new field(array('name' => 'require_auth', 'validator' => array('isCheckbox'), 'label' => lang('LANG_REQUIRE_AUTH'), 'type' => 'checkbox'));
        
        if (!$update) {
            $fields[] = new field(array('name' => 'creator', 'validator' => array('isNumber'), 'notRender' => true));
            $fields[] = new field(array('name' => 'index', 'validator' => array('isNumber'), 'notRender' => true));
            $fields[] = new field(array('name' => 'created', 'validator' => array('isRequired'), 'notRender' => true));
        }
        if ($update) {
            $fields[] = new field(array('name' => 'published', 'validator' => array('isCheckbox'), 'label' => lang('LANG_PUBLISHED'), 'notRender' => true));
        }
        $fields[] = new field(array('name' => 'body', 'label' => lang('LANG_BODY'), 'type' => 'html'));
        
        return array_merge(parent::fields($update), $fields);
    }
}
