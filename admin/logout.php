<?php
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->logout();

header('Location: index.php?logged_out=1');
exit();
?>
