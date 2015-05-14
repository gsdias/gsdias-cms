<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!IS_ADMIN && !IS_EDITOR) {
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    $defaultfields = array('title', 'description', 'tags', 'keywords', 'og_title', 'og_description', 'og_image', 'parent');

    $fields = array('show_menu', 'require_auth', 'published');

    $values = array(
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        @$_REQUEST['published'] ? @$_REQUEST['published'] : null,
    );

    $result = $csection->edit($defaultfields, $fields, $values);

    if ($result['errnum']) {
        $tpl->setvar('ERRORS', lang('LANG_PAGE_ERROR'));
        $tpl->setcondition('ERRORS');
    } else {
        $modules = array();

        foreach ($_REQUEST as $module => $value) {
            if (substr($module, 0, 10) == 'value_pm_s') {
                $vals = $value;
                $value = array();
                $moduleid = explode('_', $module);
                foreach ($vals as $i => $val) {
                    $value[] = array(
                            'value' => @$_REQUEST[$module][$i],
                            'class' => @$_REQUEST['class_'.$module][$i],
                            'style' => @$_REQUEST['style_'.$module][$i],
                    );
                }
                if (!sizeof(@$modules[$moduleid[4]])) {
                    $modules[$moduleid[4]] = array(
                        'list' => array(),
                        'style' => @$_REQUEST['style_value_pm_'.$moduleid[4]],
                        'class' => @$_REQUEST['class_value_pm_'.$moduleid[4]],
                    );
                }
                $modules[$moduleid[4]]['list'][] = $value;
            } elseif (substr($module, 0, 9) == 'value_pm_') {
                $value = array(
                    'list' => array(
                                array(
                                    array(
                                        'value' => $value,
                                        'class' => @$_REQUEST['class_'.$module],
                                        'style' => @$_REQUEST['style_'.$module],
                                    ),
                                ),
                            ),
                    'class' => '',
                    'style' => '',
                );

                $mysql->reset()
                    ->update('pagemodules')
                    ->fields(array('data'))
                    ->where('pmid = ?')
                    ->values(array(serialize($value), substr($module, 9)))
                    ->exec();
            }
        }

        if (sizeof($modules)) {
            foreach ($modules as $id => $value) {
                $mysql->reset()
                    ->update('pagemodules')
                    ->fields(array('data'))
                    ->where('pmid = ?')
                    ->values(array(serialize($value), $id))
                    ->exec();
            }
        }

        $_SESSION['message'] = sprintf(lang('LANG_PAGE_SAVED'), $_REQUEST['title']);

        header('Location: /admin/pages', true, 302);
        exit;
    }
}
