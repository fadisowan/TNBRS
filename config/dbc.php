<?php
/**
 * Created by PhpStorm.
 * User: fadi
 * Date: 4/7/2016
 * Time: 4:21 PM
 */
$servername = "localhost";
//$servername = "localhost";
$username = "root";
$password = "";
$dbname = "radius";
$conn = new mysqli($servername, $username, $password,$dbname );

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}