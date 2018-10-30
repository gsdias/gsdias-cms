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

switch($site->p('install')) {
    case 'user':
    case 'last':
    $site->main = 'STEP3';
    break;
    case 'tables':
    $site->main = 'STEP2';
    break;
    case 'database': 
    $site->main = 'STEP1';
    break;
}
    
$site->startpoint = 'index';

if ($site->p('install') === 'last') {

    $mysql->reset()
        ->insert('users')
        ->fields(array('level', 'email', 'password', 'name', 'creator'))
        ->values(array('admin', $_REQUEST['email'], md5($_REQUEST['password']), $site->p('name'), 0))
        ->exec();

    if ($mysql->total) {
        $tpl->setvar('STEP2_MESSAGES', 'Admin user saved with success. You can <a href="/admin">login</a> now.');

        define('IS_ADMIN', 1);
        $templatefiles = scandir(ASSETPATH.'/images');
        $csection = \GSD\sectionFactory::create('images');
        $_REQUEST['creator'] = $user->id;
        $_REQUEST['created'] = date('Y-m-d H:i:s', time());
        
        foreach ($templatefiles as $file) {
            if (substr($file, 0, 1) !== '.') {
                $path = ASSETPATH.'/images/'.$file;
                $file = explode('.', $file);
                $size = getimagesize($path);
            
                $valid = is_array($size);
            
                $_REQUEST['width'] = $size[0];
                $_REQUEST['height'] = $size[1];
                $_REQUEST['size'] = round(filesize($path) / 1000, 0).'KB';
            
                $_REQUEST['name'] = $file[0];
                $_REQUEST['description'] = '';
                $_REQUEST['tags'] = '';
                $_REQUEST['extension'] = $file[1];
                $result = $csection->add($file[0]);
            }
        }
        include_once 'gsd-admin/update'.PHPEXT;
    }
} else if ($site->p('install') === 'tables') {
    if ($site->p('save')) {
        $handle = @fopen(ROOTPATH.'gsd-settings'.PHPEXT, "r");
        $mysqlArray = 0;
        $string = '';
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                if ($mysqlArray > 0 && $mysqlArray < 5) {
                    if ($mysqlArray == 1) {
                        $string .= sprintf("%s'host' => '%s',\n", str_repeat(" ",8), $site->p('dbhost'));
                    }
                    if ($mysqlArray == 2) {
                        $string .= sprintf("%s'user' => '%s',\n", str_repeat(" ",8), $site->p('user'));
                    }
                    if ($mysqlArray == 3) {
                        $string .= sprintf("%s'pass' => '%s',\n", str_repeat(" ",8), $site->p('password'));
                    }
                    if ($mysqlArray == 4) {
                        $string .= sprintf("%s'db' => '%s',\n", str_repeat(" ",8), $site->p('database'));
                    }
                        $mysqlArray++;
                } else {
                    if(strpos($buffer, '$mysql')) {
                        $mysqlArray = 1;
                    }
                    $string .= $buffer;
                }
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
        file_put_contents(ROOTPATH.'gsd-settings'.PHPEXT, $string);
        $GSDConfig->mysql = array(
            'host' => $site->p('dbhost'),
            'user' => $site->p('user'),
            'pass' => $site->p('password'),
            'db' => $site->p('database'),
        );
        
        $mysql = GSD\mysqlFactory::create($site->p('database'), $site->p('dbhost'), $site->p('user'), $site->p('password'));
    }

    if (!@$GSDConfig->mysql['host']) {
        redirect('/admin?install=database');
    }

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
    } 
} else if ($site->p('install') === 'user') {
    
        $mysql->reset()
            ->select('count(*)')
            ->from('users')
            ->exec();

        if ($mysql->singleresult()) {
            $tpl->setvar('STEP2_MESSAGES', 'There is already an user on the database.');
        } else {
            $tpl->setcondition('NOUSER');
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
