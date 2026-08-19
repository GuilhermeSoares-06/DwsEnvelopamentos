<?php
session_start();
session_destroy();
header('Location: ../../ADM/login.html');
exit;
?>