<?php

abstract class section implements isection {

    public $item = array();

    public function __construct ($id = null) {

        return 0;
    }

    public function getlist ($numberPerPage = 10) {}

    public function getcurrent ($id = 0) {}

    public function generatefields ($section) {
        global $tpl, $mysql;

        $func = $section . 'fields';
        $item = $this->item;

        $sectionextrafields = function_exists($func) ? $func() : array();
        if (sizeof($sectionextrafields)) {
            $extrafields = array();

            foreach ($sectionextrafields['list'] as $key => $extrafield) {

                $extraclass = '';

                switch ($sectionextrafields['types'][$key]) {
                    case 'image':
                    $mysql->statement('SELECT * FROM images WHERE iid = ?;', array(@$item[$extrafield]));
                    $image = $mysql->singleline();

                    $image = new image(array(
                        'src' => sprintf('/gsd-assets/images/%s.%s', @$image['iid'], @$image['extension']),
                        'height' => '100',
                        'width' => 'auto',
                        'class' => sprintf('preview %s', @$item[$extrafield] ? '' : 'is-hidden')
                    ));

                    $partial = new tpl();
                    $partial->setvars(array(
                        'LABEL' => $sectionextrafields['labels'][$key],
                        'NAME' => $extrafield,
                        'IMAGE' => $image,
                        'VALUE' => @$item[$extrafield] ? @$item[$extrafield] : 0,
                        'EMPTY' => @$item[$extrafield] ? 'is-hidden' : ''
                    ));
                    $partial->setfile('_image');

                    $field = $partial;
                    $extraclass = 'image';
                    break;
                    case 'select':
                    $field = new select(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'list' => $sectionextrafields['values'],
                        'label' => $sectionextrafields['labels'][$key],
                        'selected' => @$item[$extrafield]
                    ));
                    break;
                    default:
                    $field = (string)new input(array(
                        'id' => $extrafield,
                        'name' => $extrafield,
                        'value' => @$item[$extrafield],
                        'label' => $sectionextrafields['labels'][$key]
                    ));
                    break;
                }

                $extrafields[] = array('FIELD' => $field, 'EXTRACLASS' => $extraclass);
            }

            $tpl->setarray('FIELD', $extrafields);
            $tpl->setcondition('EXTRAFIELDS');
        }
    }

    public function generatepaginator ($pages) {
        global $tpl;

        $first_page = new anchor(array('text' => '&lt; Primeira', 'href' => '?page=1'));
        $prev_page = new anchor(array('text' => 'Anterior', 'href' => '?page=' . $pages['PREV']));
        $next_page = new anchor(array('text' => 'Seguinte', 'href' => '?page=' . $pages['NEXT']));
        $last_page = new anchor(array('text' => 'Ultima &gt;', 'href' => '?page=' . $pages['LAST']));
        $tpl->setvars(array(
            'FIRST_PAGE' => $first_page,
            'PREV_PAGE' => $prev_page,
            'NEXT_PAGE' => $next_page,
            'LAST_PAGE' => $last_page,
            'CURRENT_PAGE' => $pages['CURRENT'],
            'TOTAL_PAGES' => $pages['TOTAL']
        ));
    }
    
    protected function extrafields () {
        
        $section = str_replace('client', '', get_class($this));
        
        $_fields = $section . 'fields';
        
        $fields = function_exists($_fields) ? $_fields() : array('list' => array());

        return $fields['list'];
    }
    
    public function add ($defaultfields, $defaultsafter, $defaultvalues) {
        global $mysql;
        
        $section = str_replace('client', '', get_class($this));

        $extrafields = $this->extrafields ();

        $fields = array_merge($defaultfields, $extrafields);

        $values = array();

        foreach ($fields as $field) {
            $values[] = $_REQUEST[$field];
        }

        $fields = array_merge($fields, $defaultsafter);

        $values = array_merge($values, $defaultvalues);    

        $questions = str_repeat(", ? ", sizeof($fields));

        $mysql->statement(sprintf('INSERT INTO %s (%s) values (%s);', $section, implode(', ', $fields), substr($questions, 2)), $values);
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $mysql->lastinserted());
    }
    
    public function edit ($defaultfields) {
        global $mysql, $site;
        
        $section = str_replace('client', '', get_class($this));
        
        $extrafields = $this->extrafields ();

        $fields = '';

        $values = array();

        $allfields = array_merge($defaultfields, $extrafieldslist);

        foreach ($allfields as $field) {
            $fields .= sprintf(", `%s` = ?", $field);
            $values[] = @$_REQUEST[$field];
        }

        $values[] = $site->arg(2);

        $mysql->statement(sprintf('UPDATE %s SET %s WHERE %sid = ?;', $section, substr($fields, 2), substr($section, 0, 1)), $values);
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
    }
    
    public function remove () {
        global $mysql, $site;
        
        $section = str_replace('client', '', get_class($this));
        
        $mysql->statement(sprintf('DELETE FROM %s WHERE %sid = ?;', $section, substr($section, 0, 1)), array($site->arg(2)));
        
        return array('total' => $mysql->total, 'errnum' => $mysql->errnum, 'id' => $site->arg(2));
    }
}
