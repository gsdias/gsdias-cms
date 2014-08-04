<?php

$mysql->statement('UPDATE users SET disabled = 1 WHERE uid = ?;', array($path[2]));

header("Location: /admin/users", true, 302);
