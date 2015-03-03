<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $site->main = 'STEP2';
    $site->startpoint = 'index';
    
    $mysql->statement("INSERT INTO users (level, email, password, name, creator) VALUES ('admin', :email, md5(:password), :name, 0);", array(':email' => $_REQUEST['email'], ':password' => $_REQUEST['password'], ':name' => $_REQUEST['name']));
    
    if ($mysql->total) {
        $tpl->setvar('STEP2_MESSAGES', "Admin user saved with success. You can login now. Don't forget to remove install files.");
    }

} else {
    $site->main = 'STEP1';
    $site->startpoint = 'index';
    
    $mysql->statement("SHOW DATABASES;");

    $database[@$_mysql['db']] = 1;

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
    
    if ($mysql->total) {
        
        $found = serialize($mysql->result());
        $table_exists = array();
        foreach($tables as $table => $value) {
            if (isset($tables[$table])) {
                if (strpos($found, sprintf('"%s"', $table)) !== false) {
                    $status = '<span style="color: green;">Exists</span><br>';
                    $tables[$table] = 0;
                } else {
                    $status = '<span style="color: red;">Don\'t exist</span><br>';
                }
                echo $table . ' ' . $status . '<br>';
                $table_exists[] = array(
                    'NAME' => $table,
                    'STATUS' => $status
                );
            }
        }
        $tpl->setarray('TABLE_EXISTS', $table_exists);
    }

    if (in_array(1, $tables)) {
        $tpl->setcondition('CREATETABLES');
        $table_exists = array();
        foreach($tables as $table => $value) {
            $table_exists[] = array(
                'NAME' => $table,
                'STATUS' => createtable($table)
            );
        }
        $tpl->setarray('CREATETABLES', $table_exists);

        if (is_file(CLIENTPATH . 'install' . PHPEXT)) {
            include_once(CLIENTPATH . 'install' . PHPEXT);
        }
    } else {
        $site->main = 'STEP2';
        $site->startpoint = 'index';

        $mysql->statement("SELECT count(*) FROM users;");
        if ($mysql->singleresult()) {
            $tpl->setvar('STEP2_MESSAGES', "There is already an user on the database.");
        } else {
            $tpl->setcondition('NOUSER');
        }
    }

    $dir = ROOTPATH . 'gsd-assets';

    if (!is_dir($dir)) {
        mkdir($dir, 0755);
        mkdir($dir . '/images', 0755);
        mkdir($dir . '/documents', 0755);
    }
}

if (!DEBUG) {
    $tpl->includeFiles('_DEBUG');
}

function createtable ($table) {
    global $mysql;
    $sentence = file_get_contents(sprintf('gsd-sql/table_%s.sql', $table));
    $mysql->statement('SET foreign_key_checks = 0;' . $sentence . 'SET foreign_key_checks = 1;');
    
    //printf('<pre>%s%s</pre><br>', $sentence, $mysql->errmsg);
    if (!$mysql->errnum) {
        return sprintf('<span style="color: green;">Created</span><br>', $mysql->errmsg);
    } else {
        return sprintf('<span style="color: red;">Something got wrong</span><br>', $mysql->errmsg);
    }
}
