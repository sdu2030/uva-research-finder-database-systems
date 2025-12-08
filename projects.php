<?php
session_start();
require('connect-db.php');
require('project-db.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "projects.php test OK";
?>
