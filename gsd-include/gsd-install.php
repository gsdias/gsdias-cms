<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
$tpl->setvar('HTML_CLASS', 'gsd');
if ($site->p('save')) {
    $site->main = 'STEP2';
    $site->startpoint = 'index';

    $mysql->reset()
        ->insert('users')
        ->fields(array('level', 'email', 'password', 'name', 'creator'))
        ->values(array('admin', $_REQUEST['email'], md5($_REQUEST['password']), $site->p('name'), 0))
        ->exec();

    if ($mysql->total) {
        $tpl->setvar('STEP2_MESSAGES', 'Admin user saved with success. You can <a href="/admin">login</a> now.');
        include_once 'gsd-admin/update'.PHPEXT;
    }
} else {
    $site->main = 'STEP1';
    $site->startpoint = 'index';

    $mysql->reset()
        ->show('DATABASES')
        ->exec();

    $database[@$GSDConfig->mysql['db']] = 1;

    if ($mysql->total) {
        foreach ($mysql->result() as $db) {
            if ($db->Database == $GSDConfig->mysql['db']) {
                $database[$GSDConfig->mysql['db']] = 0;
            }
        }
    } else {
        $mysql->statement(sprintf('CREATE DATABASE IF NOT EXISTS %s;', $GSDConfig->mysql['db']));
        $mysql->usedb($GSDConfig->mysql['db']);
    }

    if ($database[$GSDConfig->mysql['db']]) {
        $mysql->statement(sprintf('CREATE DATABASE IF NOT EXISTS %s', $GSDConfig->mysql['db']));
        $mysql->usedb($GSDConfig->mysql['db']);
    }

    $mysql->show('TABLES')
        ->exec();

    if ($mysql->total) {
        $found = serialize($mysql->result());
        $table_exists = array();
        foreach ($GSDConfig->tables as $table => $value) {
            if (isset($GSDConfig->tables[$table])) {
                if (strpos($found, sprintf('"%s"', $table)) !== false) {
                    $status = '<span style="color: green;">Exists</span><br>';
                    $GSDConfig->tables[$table] = 0;
                } else {
                    $status = '<span style="color: red;">Don\'t exist</span><br>';
                }

                $table_exists[] = array(
                    'NAME' => $table,
                    'STATUS' => $status,
                );
            }
        }
        $tpl->setarray('TABLE_EXISTS', $table_exists);
    }

    if (in_array(1, $GSDConfig->tables)) {
        $tpl->setcondition('CREATETABLES');
        $table_exists = array();
        foreach ($GSDConfig->tables as $table => $value) {
            $table_exists[] = array(
                'NAME' => $table,
                'STATUS' => createtable($table),
            );
        }
        $tpl->setarray('CREATETABLES', $table_exists);

        if (is_file(CLIENTPATH.'install'.PHPEXT)) {
            include_once CLIENTPATH.'install'.PHPEXT;
        }
    } else {
        $site->main = 'STEP2';
        $site->startpoint = 'index';

        $mysql->reset()
            ->select('count(*)')
            ->from('users')
            ->exec();

        if ($mysql->singleresult()) {
            $tpl->setvar('STEP2_MESSAGES', 'There is already an user on the database.');
        } else {
            $tpl->setcondition('NOUSER');
        }
    }

    if (!is_dir(ASSETPATH)) {
        mkdir(ASSETPATH, 0755);
        mkdir(ASSETPATH.'/images', 0755);
        mkdir(ASSETPATH.'/documents', 0755);
    }

    if (!function_exists('_')) {
    
        $templatefiles = scandir('gsd-locale');

        foreach ($templatefiles as $file) {
            if (substr($file, 0, 1) !== '.') {
                $cmslocalepath = ROOTPATH."/gsd-locale/".$file."/LC_MESSAGES/";
                $content = file_get_contents($cmslocalepath."/messages.po");
                $myfile = fopen($cmslocalepath."/messages.ini", "w") or die("Unable to open file!");
                preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);
        
                fwrite($myfile, "[".$file."]\n");
        
                foreach ($matches as $match) {
                    if ($match[1] !== "" && $match[2] !== "") {
                        fwrite($myfile, $match[1]." = \"".$match[2]."\"\n");
                    }
                }
                fclose($myfile);
                $clientlocalepath = CLIENTPATH."/locale/".$file."/LC_MESSAGES/";
                if (file_exists($clientlocalepath."extended.po")) {
                    $content = file_get_contents($clientlocalepath."extended.po");
                    $myfile = fopen($clientlocalepath."extended.ini", "w") or die("Unable to open file!");
                    preg_match_all('#msgid "(.*)"\nmsgstr "(.*)"#m', $content, $matches, PREG_SET_ORDER);
        
                    fwrite($myfile, "[".$file."]\n");
        
                    foreach ($matches as $match) {
                        if ($match[1] !== "" && $match[2] !== "") {
                            fwrite($myfile, $match[1]." = \"".$match[2]."\"\n");
                        }
                    }
                    fclose($myfile);
                }
            }
        }   
    } 
}

function createtable($table)
{
    global $mysql;
    $sentence = file_get_contents(sprintf('gsd-sql/table_%s.sql', $table));
    $mysql->statement('SET foreign_key_checks = 0;'.$sentence.'SET foreign_key_checks = 1;');

    if (!$mysql->errnum) {
        return sprintf('<span style="color: green;">Created</span><br>', $mysql->errmsg);
    } else {
        return sprintf('<span style="color: red;">Something got wrong</span><br>', $mysql->errmsg);
    }
}
