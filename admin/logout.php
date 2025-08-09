<?php
/**
 * Admin Logout
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';

$auth = new Auth();
$auth->logout();
?>