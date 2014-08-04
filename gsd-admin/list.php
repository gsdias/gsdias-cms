<?php

$section = $path[1];
$id = $path[2];
$action = @$path[3];

//ACTION DETECTED
if ($action) {
    include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);

    $sectionextrafields = sprintf('%sfields', $section);
    
    $sectionextrafields = function_exists($sectionextrafields) ? $sectionextrafields() : array();

    $mysql->statement(sprintf('SELECT %s.*, %s.created FROM %s LEFT JOIN users AS u ON %s.creator = u.uid WHERE %s.%sid = :id ORDER BY %s.%sid;', $section, $section, $section, $section, $section, substr($section, 0, 1), $section, substr($section, 0, 1)), array(':id' => $path[2]));

    if ($mysql->total) {
        
        $item = $mysql->singleline();
        $created = explode(' ', $item['created']);
        
        $fields = array();
        foreach ($item as $field => $value) {
            if (is_numeric($field)) {
                continue;
            }
            $fields['CURRENT_' . strtoupper(substr($section, 0, -1)) . '_'. strtoupper($field)] = $value;
        }

        $fields['CURRENT_' . strtoupper(substr($section, 0, -1)) . '_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));
        
        $tpl->setvars($fields);
        
        if (sizeof($sectionextrafields)) {
            $extrafields = array();

            foreach ($sectionextrafields['list'] as $key => $extrafield) {
                
                if (sizeof(@$sectionextrafields['values'])) {
                    $field = new select(array('id' => $extrafield, 'name' => $extrafield, 'list' => $sectionextrafields['values'], 'label' => $sectionextrafields['labels'][$key], 'selected' => @$item[$extrafield]));
                } else {
                    $field = new input(array('id' => $extrafield, 'name' => $extrafield, 'value' => @$item[$extrafield], 'label' => $sectionextrafields['labels'][$key]));
                }
                $extrafields[] = array('FIELD' => $field);
            }

            $tpl->setarray('FIELD', $extrafields); 
            $tpl->setcondition('EXTRAFIELDS'); 
        }
        $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[3], PHPEXT);

        include_once($file);
        $main = sprintf('%s/%s', $path[1], $path[3]);
    } else {
        $main = '404';
    }

} else {

    //ID DETECTED
    if ($id) {
        $main = sprintf('%s/%s', $path[1], $path[2]);
        $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[2], PHPEXT);

        include_once($file);
        
    //LISTING
    } else {

        $mysql->statement(sprintf('SELECT %s.*, %s.creator AS creator_id, u.name AS creator_name FROM %s LEFT JOIN users AS u ON %s.creator = u.uid WHERE %s.disabled IS NULL ORDER BY %s.%sid;', $section, $section, $section, $section, $section, $section, substr($section, 0, 1)));

        $list = array();

        if ($mysql->total) {
            $tpl->setcondition(sprintf('%s_EXIST', strtoupper($section)));
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
            $tpl->setarray(strtoupper($section), $list);
        }
    }
}
