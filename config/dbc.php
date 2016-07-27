<?php

define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "radius");


$conn = new mysqli(HOST, USER,PASSWORD,DATABASE);

/*
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member";
*/





if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}