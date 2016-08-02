<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/2/2016
 * Time: 11:02 AM
 */



function isUserExist($username){
    require  'config/dbc.php';

    $userexists  = "SELECT * FROM  radius.radcheck WHERE username='$username'";
    $rs_userexists = mysqli_query($conn, $userexists);

    $data_userexists = mysqli_fetch_array($rs_userexists, MYSQLI_NUM);


    if ($data_userexists[0] > 1) {

        return true;
    } else {

        return false;
    }


}
