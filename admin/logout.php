<?php
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->logout();

header('Location: login.php?logged_out=1');
exit();
?>
