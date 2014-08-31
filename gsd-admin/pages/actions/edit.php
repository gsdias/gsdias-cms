<?php

if (@$_REQUEST['save']) {

    $defaultfields = array('title', 'description', 'tags', 'keywords', 'og_title', 'og_description', 'og_image', 'show_menu', 'require_auth', 'published');

    $_REQUEST['show_menu'] = @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null;
    $_REQUEST['require_auth'] = @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null;
    $_REQUEST['published'] = @$_REQUEST['published'] ? @$_REQUEST['published'] : null;

    
    $result = $csection->edit($defaultfields);

    if ($result['errnum']) {

        $tpl->setvar('ERRORS', '{LANG_PAGE_ERROR}');
        $tpl->setcondition('ERRORS');
    } else {

        foreach ($_REQUEST as $module => $value) {

            if (strpos($module, 'pm_s') !== false) {
                $vals = $value;
                $value = array();
                foreach ($vals as $i => $val) {
                    $value[] = array(
                        'value' => @$_REQUEST[$module][$i],
                        'class' => @$_REQUEST['class_' . $module][$i],
                        'style' => @$_REQUEST['style_' . $module][$i]
                    );
                }
                $mysql->statement('UPDATE pagemodules SET data = ? WHERE pmid = ?;', array(serialize($value), substr($module, 4)));
            } else if (strpos($module, 'pm_') !== false) {
                $value = array(
                    'value' => $value,
                    'class' => @$_REQUEST['class_' . $module],
                    'style' => @$_REQUEST['style_' . $module]
                );
                $mysql->statement('UPDATE pagemodules SET data = ? WHERE pmid = ?;', array(serialize($value), substr($module, 3)));

            }
        }

        $_SESSION['message'] = '{LANG_PAGE_SAVED}';

        //header("Location: /admin/pages", true, 302);
        //exit;
    }
}
