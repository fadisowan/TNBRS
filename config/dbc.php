<?php

/*define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "radius");*/
$HOST="localhost";
$USER="root";
$PASSWORD="";
$DATABASE="radius";

$conn = new mysqli($HOST, $USER,$PASSWORD,$DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

