<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
}

$mysql->statement('DELETE FROM pages WHERE pid = ?;', array($path[2]));

header("Location: /admin/pages", true, 302);
