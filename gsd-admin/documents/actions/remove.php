<?php

removefile(ASSETPATH . 'documents/' . $path[2]);

$mysql->statement('DELETE FROM documents WHERE did = ?;', array($path[2]));

if ($mysql->total) {
    header("Location: /admin/documents", true, 302);
    exit;
}
