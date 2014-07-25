<?php

include_once('settings.php');
include_once('config' . PHPEXT);

if (@$_REQUEST['save']) {
    $main = 'STEP2';
    
    $mysql->statement("INSERT INTO users (level, email, password, name) VALUES (100, :email, md5(:password), :name);", array(':email' => $_REQUEST['email'], ':password' => $_REQUEST['password'], ':name' => $_REQUEST['name']));
    if ($mysql->total) {
        printf("Admin user saved with success. You can login now.");
    }

} else {
    $main = 'STEP1';
    
    $mysql->statement("SHOW DATABASES");

    $database[$_mysql['db']] = 1;

    if ($mysql->total) {
        foreach($mysql->result() as $db) {
            if ($db[0] == $_mysql['db']) {
                $database[$_mysql['db']] = 0;
            }
        }
    } else {
        $mysql->statement(sprintf("CREATE DATABASE IF NOT EXISTS %s;", $_mysql['db']));
        $mysql->statement(sprintf("Use %s;", $_mysql['db']));
    }

    if ($database[$_mysql['db']]) {
        $mysql->statement(sprintf("CREATE DATABASE IF NOT EXISTS %s", $_mysql['db']));
        $mysql->statement(sprintf("Use %s;", $_mysql['db']));
    }

    $mysql->statement('SHOW TABLES;');

    $tables = array(
        'users' => 1
    );
    
    if ($mysql->total) {
        
        $found = serialize($mysql->result());
        $table_exists = array();
        foreach($tables as $table => $value) {
            if (isset($tables[$table])) {
                if (strpos($found, sprintf('"%s"', $table)) !== false) {
                    $status = '<span style="color: green;">Exists</span><br>';
                    $tables[$table] = 0;
                } else {
                    $status = '<span style="color: red;">Dont\' exist</span><br>';
                }
                
                $table_exists[] = array(
                    'NAME' => $table,
                    'STATUS' => $status
                );
            }
        }
        $tpl->setarray('TABLE_EXISTS', $table_exists);
    }
    if (in_array(1, $tables)) {
        define('CREATETABLES', 1);
        $table_exists = array();
        foreach($tables as $table => $value) {
            $table_exists[] = array(
                'NAME' => $table,
                'STATUS' => createtable($table)
            );
        }
        $tpl->setarray('CREATETABLES', $table_exists);
    } else {
        $main = 'STEP2';
    }
}
$tpl->includeFiles('MAIN', $main);
$tpl->setFile('INDEX');

if (!DEBUG) {
    $tpl->includeFiles('_DEBUG');
}

echo $tpl;

function createtable ($table) {
    global $mysql, $tpl;
    
    $mysql->statement(file_get_contents(sprintf('sql/table_%s.sql', $table)));
    
    if ($mysql->executed) {
        return sprintf('<span style="color: green;">Created</span><br>');
    } else {
        return sprintf('<span style="color: red;">Something got wrong</span><br>');
    }
}
?>