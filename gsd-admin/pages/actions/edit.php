<?php

if (@$_REQUEST['save']) {
    
    $extrafields = function_exists('pagesfields') ? pagesfields() : array();

    $defaultfields = array('title', 'description', 'tags', 'keywords', 'og_title', 'og_description', 'og_image');

    $extrafieldslist = sizeof(@$extrafields['list']) ? $extrafields['list'] : array();

    $values = array();
    $fields = '';

    $allfields = array_merge($defaultfields, $extrafieldslist);

    foreach ($allfields as $field) {
        $fields .= sprintf(", `%s` = ?", $field);
        $values[] = @$_REQUEST[$field];
    }

    $fields .= ', `show_menu` = ?, `require_auth` = ?, `published` = ?';

    $values[] = @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null;
    $values[] = @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null;
    $values[] = @$_REQUEST['published'] ? @$_REQUEST['published'] : null;
    $values[] = $site->arg(2);

    $mysql->statement(sprintf('UPDATE pages SET %s WHERE pid = ?;', substr($fields, 2)), $values);

    if ($mysql->errnum) {
    
        $tpl->setvar('ERRORS', 'Houve um problema ao salvar as definicoes. Verifique os dados e tente novamente');
        $tpl->setcondition('ERRORS');
        
    } else {
    
        $_SESSION['message'] = 'Pagina salva.';

        header("Location: /admin/pages", true, 302);
        exit;
        
    }
}
