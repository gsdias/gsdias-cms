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
            if (strpos($module, 'pm_') !== false) {
                $mysql->statement('UPDATE pagemodules SET data = ? WHERE pmid = ?;', array($value, substr($module, 3)));
            }
        }

        $_SESSION['message'] = '{LANG_PAGE_SAVED}';

        header("Location: /admin/pages", true, 302);
        exit;
    }
}
