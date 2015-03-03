<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

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

        $modules = array();

        foreach ($_REQUEST as $module => $value) {

            if (substr($module, 0, 10) == 'value_pm_s') {
                $vals = $value;
                $value = array();
                $moduleid = explode('_', $module);
                foreach ($vals as $i => $val) {
                    $value[] = array (
                            'value' => @$_REQUEST[$module][$i],
                            'class' => @$_REQUEST['class_' . $module][$i],
                            'style' => @$_REQUEST['style_' . $module][$i]
                    );
                }
                if (!sizeof(@$modules[$moduleid[4]])) {
                    $modules[$moduleid[4]] = array();
                }
                $modules[$moduleid[4]][] = $value;
                $modules[$moduleid[4]]['order'] = 1;

            } else if (substr($module, 0, 9) == 'value_pm_') {
                $value = array(
                            array(
                                array(
                                    'value' => $value,
                                    'class' => @$_REQUEST['class_' . $module],
                                    'style' => @$_REQUEST['style_' . $module]
                                )
                            )
                );

                $mysql->statement('UPDATE pagemodules SET data = ? WHERE pmid = ?;', array(serialize($value), substr($module, 9)));

            }
        }

        if (sizeof($modules)) {
            foreach ($modules as $id => $value) {
                $mysql->statement('UPDATE pagemodules SET data = ? WHERE pmid = ?;', array(serialize($value), $id));
            }
        }

        $_SESSION['message'] = '{LANG_PAGE_SAVED}';

        header("Location: /admin/pages", true, 302);
        exit;
    }
}
