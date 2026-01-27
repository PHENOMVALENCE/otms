<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "otms";


$connection = mysqli_connect($servername, $username, $password, $dbname);


if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>