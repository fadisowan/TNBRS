<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 7/24/2016
 * Time: 10:50 AM
 */
/*
require_once 'config/parm.php';
require  'config/dbc.php';*/


    $path="./cron/users.txt";
    $isCreated=true;

/*    if($isCreated){
        echo "file Created:  $path";

    }else{
        echo "file not creatd";
    }*/

$handle = fopen("users.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = str_replace("\n", "", $line);
    }
    fclose($handle);
}