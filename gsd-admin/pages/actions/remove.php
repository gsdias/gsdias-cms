<?php

$mysql->statement('UPDATE pages SET disabled = 1 WHERE pid = ?;', array($path[2]));

header("Location: /admin/pages", true, 302);
