<?php

/**
 * File - global.inc.php
 * Author - Saurabh Singh
 * Purpose - Site Configuration
 * Version - 0.1
 */

require "vendor/autoload.php";

// load env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Site Configuration.
$db_host = $_ENV["DB_HOST"];  // Database Host
$db_username = $_ENV["DB_USERNAME"];       // Database user
$db_password = $_ENV["DB_PASSWORD"];    // Database password
$db_name = $_ENV["DB_NAME"];      // Database name

$GlobalApplication = $_ENV["GLOBAL_APPLICATION"]; // Application path

// Connection with Database.
$db = mysqli_connect($db_host, $db_username, $db_password, $db_name) or die("Sorry! We are not able to connect the DB");

// To manage session Session start.
session_start();
