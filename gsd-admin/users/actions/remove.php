<?php

$mysql->statement('DELETE FROM users WHERE uid = ?;', array($path[2]));

header("Location: /admin/users", true, 302);
