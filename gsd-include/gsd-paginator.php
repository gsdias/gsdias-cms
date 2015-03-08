<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

function pageTotal ($sql, $numberPerPage) {
    global $mysql;
    $select = sprintf('SELECT floor(count(*) / %d), mod(count(*), %d) %s', $numberPerPage, $numberPerPage, $sql);
    
    $mysql->statement($select);
    
    $result = $mysql->singleline();

    $pages = $result[0];
    $remain = $result[1];

    $pages = $remain > 0 ? ++$pages : $pages;
    return $pages;
}

function pageNumber () {

    return @$_REQUEST['page'] ? $_REQUEST['page'] : 1;
}

function pageLimit ($number = 0, $numberPerPage = 50) {
    $limit = ($number - 1) * $numberPerPage;
    $limit = $limit < 0 ? 0 : $limit;

    return ' LIMIT ' . $limit . ', ' . $numberPerPage;
}

function pageGenerator ($sql, $NumberPerPage = 10) {

    $pages = pageTotal($sql, $NumberPerPage);

    return array (
        'PREV' => pageNumber() > 1 ? pageNumber() - 1 : 1,
        'NEXT' => pageNumber () < $pages ? pageNumber() + 1 : $pages,
        'CURRENT' => pageNumber(),
        'TOTAL' => $pages,
        'LAST' => $pages
    );
}
