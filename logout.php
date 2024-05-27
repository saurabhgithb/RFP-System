<?php
/**
* File - logout.php
* Author - Saurabh Singh
* Purpose - To destroy session / logout student
* Version - 0.1
*/

//Include configuration settings
require_once('includes/configuration.php');

session_destroy();
header("Location: index.php");
?>