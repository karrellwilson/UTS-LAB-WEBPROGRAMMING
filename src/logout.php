<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SESSION = [];

session_destroy();

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

header('Location: ../index.php');
exit();
?>
