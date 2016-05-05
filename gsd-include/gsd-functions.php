<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
function GSDClassLoading($className)
{
    $className = str_replace(array('GSD\\Extended\\', 'GSD\\Api\\Extended\\', 'GSD\\Api\\', 'GSD\\'), array(CLIENTCLASSPATH, CLIENTPATH.'api/', ROOTPATH.'gsd-api/', CLASSPATH), $className);

    if (is_file($className.PHPEXT)) {
        include_once $className.PHPEXT;
    }
}

function redirect($path = '/', $code = 302)
{
    header('Location: '.$path, true, $code);
    exit;
}

function escapeText($value = '', $encoding = 'UTF-8')
{
    return htmlspecialchars($value,ENT_QUOTES | ENT_HTML401, $encoding);
}

function isFile($value = '', $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field->getName()));
    $result = array(
        'result' => isset($_FILES[$field->getName()]),
        'value' => 1,
        'field' => null,
        'message' => sprintf('(%s) Invalid type. Needs to be a file', $label)
    );
    
    $field->setName(null);

    return $result;
}

function isString($value = '', $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field->getName()));
    $result = array(
        'result' => is_string($value),
        'value' => escapeText($value),
        'field' => $field->getName(),
        'message' => sprintf('(%s) Invalid type. Needs to be a string', $label)
    );
    
    return $result;
}

function isRequired($value = '', $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field->getName()));
    $result = array(
        'result' => trim($value) !== '',
        'value' => $value,
        'field' => $field->getName(),
        'message' => sprintf('(%s) Is required', $label)
    );
    
    return $result;
}

function isEmail($value = '', $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field->getName()));
    $result = array(
        'result' => !$value || filter_var($value, FILTER_VALIDATE_EMAIL),
        'value' => $value,
        'field' => $field->getName(),
        'message' => sprintf('(%s) Needs to be a valid email', $label)
    );

    return $result;
}

function isUrl($value = '', $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field[0]));
    $result = array(
        'result' => !$value || filter_var($value, FILTER_VALIDATE_URL),
        'value' => $value,
        'field' => $field->getName(),
        'message' => sprintf('(%s) Needs to be a valid url', $label)
    );

    return $result;
}

function isNumber($value = 0, $field = '', $label = '')
{
    $label = $label ? $label : lang('LANG_'.strtoupper($field->getName()));
    $value = $value === '' ? 0 : $value;
    $result = array(
        'result' => is_numeric($value),
        'value' => $value,
        'field' => $field->getName(),
        'message' => sprintf('(%s) Invalid type. Needs to be a number', $label)
    );

    return $result;
}

function isPassword($value = '', $field = '')
{
    $result = array(
        'result' => 1,
        'value' => md5($value),
        'field' => $field->getName(),
        'message' => ''
    );

    return $result;
}

function isCheckbox($value = '', $field = '')
{
    $result = array(
        'result' => 1,
        'value' => $value ? 1 : null,
        'field' => $field->getName(),
        'message' => ''
    );

    return $result;
}

function lang($text, $option = 'NONE')
{
    global $site;

    if (function_exists('_')) {
        if (@$site->isFrontend) {
            $translated = dcgettext('extended', $text, LC_MESSAGES);
            $translated = $translated != $text ? $translated : _($text);
        } else {
            $translated = _($text);
            $translated = $translated != $text ? $translated : dcgettext('extended', $text, LC_MESSAGES);
        }
    } else {
        $translated = $text;
    }

    switch ($option) {
        case 'CAMEL':
            $translated = ucwords($translated);
        break;
        case 'UPPER':
            $translated = mb_strtoupper($translated, 'UTF-8');
        break;
        case 'LOWER':
            $translated = mb_strtolower($translated, 'UTF-8');
        break;
        case 'FIRST':
            $translated = ucfirst($translated);
        break;
    }

    return $translated;
}

function isuploaded($folder, $filename)
{
    $found = '';
    if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, $filename) > -1) {
                $found = $folder.'/'.$file;
            }
        }
        closedir($handle);
    }

    return $found;
}

/**
 * @desc compare extension file with given list
 *
 * @param string $filename - name of the file
 * @param array $allowedtypes - list of allowed types
 *
 * @return bool - success or failure
 */
function checkfiletype($filename, $allowedtypes = array())
{
    $allowed = 0;
    $filename = explode('.', $filename);
    foreach ($allowedtypes as $type) {
        if (strtolower($type) == strtolower($filename[sizeof($filename) - 1])) {
            $allowed = 1;
        }
    }

    return $allowed;
}

/**
 * @desc trims given text with given length
 *
 * @param string $text - text to be treated
 * @param int $length - number characters before trims the text
 *
 * @return string - trimmed text with ellipsis if needed
 */
function trimtext($text, $length)
{
    return mb_strlen($text) < $length ? $text : mb_substr($text, 0, mb_strpos($text, ' ', $length)).'(...)';
}

/**
 * @desc changes the name of a given file keeping the same extension
 *
 * @param string $old - current name of file with extension
 * @param string $new - name of file to be changed
 *
 * @return string - new name with same extension
 */
function filerename($old, $new)
{
    $old = explode('.', $old);

    return $new.'.'.strtolower($old[sizeof($old) - 1]);
}

/**
 * @desc presents the date with the right format (DD/MM/YYYY)
 *
 * @param string $date - date
 *
 * @return string $date - formated date
 */
function dateformat($date = '')
{
    $express = '/^([\d]{1,2})-([\d]{1,2})-([\d]{4})$/';

    preg_match($express, $date, $matches);
    if (sizeof($matches) === 4) {
        $date = preg_replace($express, '$3-$2-$1', $date);
    }

    return $date;
}

/**
 * @desc calculates difference between to dates
 *
 * @param string $first - first date
 * @param string $second - second date
 *
 * @return int - result of the difference in days
 */
function dateDif($first = null, $second = null)
{
    $first = isDate($first) ? explode('-', dateformat($first)) : time();
    $second = isDate($second) ? explode('-', dateformat($second)) : time();

    return mktime('0', '0', '0', $second[1], $second[2], $second[0]) - mktime('0', '0', '0', $first[1], $first[2], $first[0]);
}

function timeago($seconds = 0, $hour = 0)
{
    global $lang, $config;

    $days = ceil($seconds / 3600 / 24);
    $months = $days > 30 ? $days / 30 : 0;
    $months = round($months, 0);

    if ($months > 0) {
        $label = sprintf('%d %s %s', $months, $months > 1 ? sprintf(' %s', lang('LANG_MONTHS')) : lang('LANG_MONTH'), lang('LANG_AGO'));
    } else {
        $label = $days > 0 ? $days.($days == 1 ? sprintf(' %s ', lang('LANG_DAY')) : sprintf(' %s ', lang('LANG_DAYS'))).lang('LANG_AGO') : sprintf('%s ', lang('LANG_AT')).date('H:i', strtotime($hour));
    }

    return $label;
}

/**
 * @desc checks if the given input is a valid date
 *
 * @param string $date - given date
 *
 * @return bool - success of failure
 */
function isDate($date = null)
{
    $date = str_replace(array('/', '.'), array('-', '-'), $date);

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
 *
 * @param string $date - given date
 *
 * @return string - changed date
 */
function datapt($date = null)
{
    $date = str_replace(array('Feb', 'Apr', 'May', 'Aug', 'Sep', 'Oct', 'Dec'), array('Fev', 'Abr', 'Mai', 'Ago', 'Set', 'Out', 'Dez'), $date);

    return $date;
}

/**
 * Formata uma data.
 */
function data($data = null)
{
    $data = $data ? explode('-', $data) : $data;
    $data[1] = str_replace(array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'), array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'), $data[1]);

    return $data[2].' de '.$data[1].' de '.$data[0];
}

/**
 * Inverte o formato de uma data.
 */
function reverseData($data)
{
    if (!$data) {
        return;
    }
    $data = str_replace(array('/', '.'), array('-', '-'), $data);
    $data = explode('-', $data);

    return $data[2].'-'.$data[1].'-'.$data[0];
}

/**
 * Inverte o formato de uma data.
 */
function smallData($data)
{
    if (!$data) {
        return;
    }
    $data = str_replace(array('/', '.'), array('-', '-'), $data);
    $data = explode('-', $data);

    return substr($data[0], 2, 2).'-'.$data[1].'-'.$data[2];
}

/**
 * @desc save file to disk
 *
 * @param string $file - name of file
 * @param string $path - internal path to save
 * @param string $type - allowed file types
 * @param string $nottype - not allowed file types
 * @param string $rename - new name of file
 *
 * @return string - return new file name if given
 */
function savefile($file, $path, $type = null, $nottype = null, $rename = null)
{
    if ($file['error'] == 0) {
        $newfilename = $file['name'];
        $typefile = explode('/', $file['type']);
        if (($type && $type == $typefile[0]) || ($nottype && $nottype != $typefile[0]) || (!$type && !$nottype)) {
            $ext = explode('.', $file['name']);
            $ext = $ext[sizeof($ext) - 1];
            if ($rename) {
                $newfilename = filerename($file['name'], $rename);
            }

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }
            
            move_uploaded_file($file['tmp_name'], $path.$newfilename);

            return $newfilename;
        }
    }

    return '';
}

#http://stackoverflow.com/questions/1334398/how-to-delete-a-folder-with-contents-using-php
function removefile($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            removefile(realpath($path).'/'.$file);
        }

        return rmdir($path);
    } elseif (is_file($path) === true) {
        return unlink($path);
    }

    return false;
}

function toAscii($str)
{
    setlocale(LC_ALL, 'en_US.UTF8');
    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_| -]+/", '-', $clean);

    return $clean;
}

function getLanguage()
{
    global $site, $languages, $user;

    $languageList = array_keys($languages);

    $browserlang = preg_replace('#;q=[0-9].[0-9]#s', '', @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $browserlang = explode(',', str_replace('-', '_', $browserlang));

    $redirect = explode('/', @$_REQUEST['redirect']);

    $list = array($site->arg(0), @$redirect[1]);

    if (!$site->isFrontend) {
        array_unshift($list, $user->locale, @$site->locale);
    }

    $list = array_merge($list, array($user->locale), $browserlang);

    $list[] = @$site->locale;

    foreach ($list as $prefered) {
        foreach ($languageList as $key) {
            $decomposed = explode('_', $key);

            if ($key === $prefered || $decomposed[0] === $prefered) {
                return $key;
            }
        }
    }

    return '';
}
