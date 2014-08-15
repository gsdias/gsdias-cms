<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $.fields = $section . 'fields';

    $defaultfields = array('title', 'url', 'description', 'keywords', 'tags', 'og_title', 'og_image', 'og_description');
    
    $extrafields = function_exists($.fields) ? $.fields() : array('list' => array());
    
    $fields = array_merge($defaultfields, $extrafields['list']);

    $values = array();
    
    foreach ($fields as $field) {
        $values[] = $_REQUEST[$field];
    }
    
    $fields = array_merge($fields, array('creator', 'require_auth', 'show_menu'));
    
    $values = array_merge($values, array(
        $user->id,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null
    ));    
        
    $questions = str_repeat(", ? ", sizeof($fields));

    $mysql->statement(sprintf('INSERT INTO pages (%s) values(%s);', implode(', ', $fields), substr($questions, 2)), $values);

    if ($mysql->total) {
        $_SESSION['message'] = sprintf('Pagina "%s" criada.', $_REQUEST['title']);
        header("Location: /admin/pages", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', 'Ja\' existe uma pagina com esse endereco.');
        $tpl->setcondition('ERRORS');
    }
}
