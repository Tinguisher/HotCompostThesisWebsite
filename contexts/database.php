<?php
// name of database and info
$host = "localhost";
$dbname = "hotcompostthesiswebsite";
$username = "root";
$password = "";

// connect through database
$mysqli = new mysqli($host, $username, $password, $dbname);

// if there is error in connection
if ($mysqli->connect_errno){
    die ("Connection error: " . $mysqli->connect_error);
}

// return if there is connection
return $mysqli;

?>