<?php

function pageTotal ($sql, $numberPerPage) {
    global $mysql;
    $select = sprintf('SELECT floor(count(*) / %d), mod(count(*), %d) %s', $numberPerPage, $numberPerPage, $sql);
    $mysql->statement($select);
    $result = $mysql->result();

    $pages = $result[0][0];
    $remain = $result[0][1];

    $pages = $remain > 0 ? ++$pages : $pages;
    return $pages;
}

function pagePath () {
    global $path;
    $fullpath = '/';

    $i = 0;
    while (isset($path[$i]) && !is_numeric($path[$i])) {
        if ($path[$i] != '') {
            $fullpath .= $path[$i] . '/';
        }
        $i++;
    }
    return $fullpath;
}

function pageNumber () {
    global $path;
    $fullpath = '/';

    $i = 0;
    while (isset($path[$i]) && !is_numeric($path[$i])) {
        if ($path[$i] != '') {
            $fullpath .= $path[$i] . '/';
        }
        $i++;
    }

    $number = isset($path[$i]) ? $path[$i] : 1;
    return $number;
}

function pageLimit ($number = 0, $numberPerPage = 50) {
    $limit = ($number - 1) * $numberPerPage;
    $limit = $limit < 0 ? 0 : $limit;

    return ' LIMIT ' . $limit . ', ' . $numberPerPage;
}

function pageGenerator ($sql, $NumberPerPage = 50, $totalPages = 10, $numberPage = 1) {
    global $tpl, $path, $lang;

    $fullpath = pagePath();

    $pages = pageTotal($sql, $NumberPerPage);

    if ($pages > 1) {
        define('VAGAS_PAGES', 1);

        $page = $pages > $totalPages && $numberPage >= floor($totalPages / 2) + 1 ? $numberPage - (floor($totalPages / 2) - 1) : 1;
        $page = $pages > $totalPages && $pages - $page < $totalPages ? $pages - ($totalPages - 1) : $page;
        $number = 1;
        $_array1 = array();
        $_pages = $pages;
        while ($pages-- > 0 && $number <= $totalPages && $page <= $_pages) {
            $_array1[$page]['LINK'] = $fullpath . $page;
            $_array1[$page]['PAGE'] = $page;
            if ($numberPage == $page) {
                $_array1[$page]['SELECTED'] = 'selected';
            }
            $page++;
            $number++;
        }
        if (1 != $numberPage) {
            $tpl->setVar('FIRST', sprintf('<li><a class="js" href="%s1">&laquo;</a></li>', $fullpath));
            $tpl->setVar('PREVIOUS', sprintf('<li><a class="js" href="%s%s">&lt;</a></li>', $fullpath, ($numberPage - 1)));
        }
        if ($numberPage!= $_pages) {
            $tpl->setVar('LAST', sprintf('<li><a class="js" href="%s%s">&raquo;</a></li>', $fullpath, $_pages));
            $tpl->setVar('NEXT', sprintf('<li><a class="js" href="%s%s">&gt;</a></li>', $fullpath, ($numberPage + 1)));
        }
        $tpl->repVar('TOTAL_PAGES', sprintf($lang[$lang['lang']]['TOTAL_PAGES'], $_pages));
        $tpl->setArray('VAGAS_PAGES', $_array1);
    }
    unset($type, $ordem, $param);
}
