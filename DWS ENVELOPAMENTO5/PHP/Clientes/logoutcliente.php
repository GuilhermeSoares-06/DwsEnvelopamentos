<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: ../../telas/Cliente/principal.html');
exit();