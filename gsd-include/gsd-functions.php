<?php

function GSDClassLoading($className) {
    if (strpos($className, 'client') === false) {
        include_once(CLASSPATH . $className . PHPEXT);
    } else {
        include_once(CLIENTPATH . 'class/' . $className . PHPEXT);
    }
}

function isuploaded ($folder, $filename) {
    $found = '';
    if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, $filename) > -1) $found = $folder . '/' . $file;
        }
        closedir($handle);
    }
    return $found;
}

/** 
  * @desc compare extension file with given list
  * @param string $filename - name of the file
  * @param array $allowedtypes - list of allowed types
  * @return bool - success or failure 
*/  
function checkfiletype ($filename, $allowedtypes = array()) {
    $allowed = 0;
    $filename = explode(".",$filename);
    foreach ($allowedtypes as $type)
        if (strtolower($type) == strtolower($filename[sizeof($filename)-1]))
        $allowed = 1;
        return $allowed;
}

/** 
  * @desc trims given text with given length
  * @param string $text - text to be treated
  * @param int $length - number characters before trims the text
  * @return string - trimmed text with ellipsis if needed
*/  
function trimtext ($text, $length) {
    return mb_strlen($text) < $length ? $text : mb_substr ($text, 0, mb_strpos($text, ' ', $length)) . '(...)';
}

/** 
  * @desc changes the name of a given file keeping the same extension
  * @param string $old - current name of file with extension
  * @param string $new - name of file to be changed
  * @return string - new name with same extension
*/  
function filerename ($old, $new) {
    $old = explode(".", $old);
    return $new . '.' . strtolower($old[sizeof($old) - 1]);
}

/** 
  * @desc creates a string with html to handle files
  * @param string $path - public path of the file on the server
  * @param string $file - public name of file
  * @param string $id - id of the file
  * @param string $name - internal name type of file
  * @param string $desc - small description about the file
  * @return string - html code ready to output
*/  
function getSend ($path, $file, $id, $name, $desc, $type = '') {

    $result = $file ? sprintf('<a href="%s%s" target="_blank" class="download-link">
    <img src="/resources/images/link.png" /></a>
    <input type="submit" data-name="%s" data-path="%s" data-value="%s" data-type="%s" name="delete" class="delete icon-cross-black">', $path, $file, $name, $path, $file, $type) : '';
    $result = sprintf('<form method="post" enctype="multipart/form-data" class="envio">
    <div id="progress">
        <div class="bar" style="width: 0%%;"></div>
    </div>
    <input name="id" type="hidden" value="%s"/>
    <input name="name" type="hidden" value="%s"/>
    <div class="input file colA">
        <button class="icon-upload"></button>
        <input class="input" data-name="%s" data-value="%s" name="%s" data-path="%s" type="file" />
        ' . $result . '
    </div>
      </form>
    ', $id, $name, $name, $file, $name, $path, $desc);
    return $result;
}

/** 
  * @desc trims given text with given length
  * @param string $text - text to be treated
  * @param int $length - number characters before trims the text
  * @return string - trimmed text with ellipsis if needed
*/  
function fileparam ($name) {
    global $files_tracking;

    $params = @$files_tracking[$name];

    return is_array($params) ? $params : array('table' => '', 'field' => '', 'fieldid' => '', 'path' => '');
}
/** 
  * @desc searches for a given country name
  * @param string $search - partial name of country
  * @return int - id of the first country found
*/  
function getCountryValue ($search) {
    global $paises;

    foreach ($paises as $id => $values) {

        if (-1 < stripos($values, $search)) {
            return $id;
        } 

    }
    return;
}

/** 
  * @desc searches for a given county name
  * @param string $search - partial name of county
  * @return int - id of the first county found
*/  
function getCountyValue ($search) {
    global $concelhos;

    foreach ($concelhos as $districtid => $district) {

        foreach ($district as $countyid => $countyname) {        

            if (-1 < stripos($countyname, $search)) {
                return substr("0" . $districtid, -2) . substr("0" . $countyid, -2);
            } 

        } 

    }
    return;
}

/** 
  * @desc presents the date with the right format (DD/MM/YYYY)
  * @param string $date - date
  * @return string $date - formated date
*/
function dateformat ($date = '') {
    $express = '/^([\d]{1,2})-([\d]{1,2})-([\d]{4})$/';

    preg_match($express, $date, $matches);
    if (sizeof($matches) === 4) {
        $date = preg_replace($express, '$3-$2-$1', $date);
    }

    return $date;
}

/**
  * @desc calculates difference between to dates
  * @param string $first - first date
  * @param string $second - second date
  * @return int - result of the difference in days
*/  
function dateDif ($first = null, $second = null) {
    $first = isDate($first) ? explode('-', dateformat($first)) : time();
    $second = isDate($second) ? explode('-', dateformat($second)) : time();
    return mktime('0', '0', '0', $second[1], $second[2], $second[0]) - mktime('0', '0', '0', $first[1], $first[2], $first[0]);
}

function timeago ($seconds = 0) {
    global $lang, $config;
    
    $days = $seconds / 3600 / 24;
    $months = $days > 30 ? $days / 30 : 0;
    $months = round($months, 0);
    
    if ($months > 0) {
        $label = sprintf('%d %s %s', $months, $months > 1 ? 'meses': 'mês', $lang[$config['lang']]['LANG_AGO']);
    } else {
        $label = sprintf('%d %s %s', $days, $days > 1 ? 'dias': 'hoje', $lang[$config['lang']]['LANG_AGO']);
    }
    
    return $label;
}

/** 
  * @desc checks if the given input is a valid date
  * @param string $date - given date
  * @return boolean - success of failure
*/  
function isDate ($date = null) {
    $date = str_replace(array("/", "."), array("-", "-"), $date);

    $express = '/^([\d]{4})-([\d]{1,2})-([\d]{1,2})$/';

    preg_match($express, $date, $matches);

    if (sizeof($matches) === 4) {
        return 1;
    }

    $express = '/^([\d]{1,2})-([\d]{1,2})-([\d]{4})$/';

    preg_match($express, $date, $matches);

    if (sizeof($matches) === 4) {
        return 1;
    }

    return 0;
}

/** 
  * @desc converts english date to portuguese
  * @param string $date - given date
  * @return string - changed date
*/  
function datapt ($date = null) {
    $date = str_replace(array('Feb', 'Apr', 'May', 'Aug', 'Sep', 'Oct', 'Dec'), array('Fev', 'Abr', 'Mai', 'Ago', 'Set', 'Out', 'Dez'), $date);
    return $date;
}

/**
 * Formata uma data
 */
function data ($data = null) {
    $data = $data ? explode('-', $data) : $data;
    $data[1] = str_replace(array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'), $data[1]);
    return $data[2] . ' de ' . $data[1] . ' de ' . $data[0];
}

/**
 * Inverte o formato de uma data
 */
function reverseData ($data) {
    if (!$data)
        return;
    $data = str_replace(array("/", "."), array("-", "-"), $data);
    $data = explode('-', $data);
    return $data[2] . '-' . $data[1] . '-' . $data[0];
}

/**
 * Inverte o formato de uma data
 */
function smallData ($data) {
    if (!$data)
        return;
    $data = str_replace(array("/", "."), array("-", "-"), $data);
    $data = explode('-', $data);
    return substr($data[0], 2, 2) . '-' . $data[1] . '-' . $data[2];
}

function sendMailRecover ($to, $link) {
    global $pathsite, $_title;
    $email = new email();
    $email->setTo($to);
    $email->setVar('link', $link);
    $email->setVar('site', $_title);
    $email->setFrom(sprintf("webmaster@sve.proatlantico.com", $pathsite));
    $email->setTemplate(TPLPATH . 'emails/recuperar_pass.html');
    return $email->sendMail();
}

/** 
  * @desc save file to disk
  * @param string $file - name of file
  * @param string $path - internal path to save
  * @param string $type - allowed file types
  * @param string $nottype - not allowed file types
  * @param string $rename - new name of file
  * @return string - return new file name if given
*/  
function savefile ($file, $path, $type = null, $nottype = null, $rename = null) {

    if ($file['error'] == 0) {
        $newfilename = $file['name'];
        $typefile = explode("/", $file['type']);
        if (($type && $type == $typefile[0]) || ($nottype && $nottype != $typefile[0]) || (!$type && !$nottype)) {
            $ext = explode(".", $file['name']);
            $ext = $ext[sizeof($ext) - 1];
            if ($rename) {
                $newfilename = filerename($file['name'], $rename);
            }

            move_uploaded_file($file["tmp_name"], $path . $newfilename);
            return $newfilename;
        }
    }
    return '';
}

function removefile ($table, $field, $idfield, $value) {
    global $mysql;
    $mysql->statement(sprintf('UPDATE %s SET %s = NULL WHERE %s = "%s"', $table, $field, $idfield, $value));
}

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'functions' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'functions' . PHPEXT);
}
