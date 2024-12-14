<?php
// Database credentials.
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'ChardiKala');
define('DB_NAME', 'CTI');

// Application constants.
const USERNAME = 'admin';
const PASSWORD = 'password';
const ENCRYPTION_KEY = '1234567890abcdef';
 
//connect to MySQL database 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>