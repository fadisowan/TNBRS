<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/2/2016
 * Time: 11:02 AM
 *
 *
 * */
function isUsersexists($username)
{
    require 'config/dbc.php';

    $userexists = "SELECT * FROM  radius.radcheck WHERE username='$username'";
    $rs_userexists = mysqli_query($conn, $userexists);

    $data_userexists = mysqli_fetch_array($rs_userexists, MYSQLI_NUM);


    if ($data_userexists[0] > 1) {

        return true;
    } else {

        return false;
    }


}

function isUserSuspended($username)
{
    require 'config/dbc.php';

    $sql_suspended = "SELECT
  radusergroup.username,
  radusergroup.groupname
FROM radusergroup
WHERE radusergroup.username = '$username'
AND radusergroup.groupname = 'daloRADIUS-Disabled-Users'
       ";

    $rs_suspended = mysqli_query($conn, $sql_suspended);


    if (mysqli_num_rows($rs_suspended) == 0) {

        return false;
    } else {
        return true;
    }
}

function isLocked($username)
{
    require 'config/dbc.php';

    $sql_locked = " SELECT  locked FROM  radcheck WHERE username='$username';
 ";





    $rs_locked = mysqli_query($conn, $sql_locked);
    $data_locked = mysqli_fetch_array($rs_locked, MYSQLI_NUM);

    if (  $data_locked [0]   == true) {

        return true;
    } else {
        return false;
    }
}




function attempt($username)
{
    require 'config/dbc.php';
    $currntAttpmt = "SELECT attempt FROM  radcheck WHERE username='$username' ; ";

    $rs_attmpt = mysqli_query($conn, $currntAttpmt);

    $data_attmpt = mysqli_fetch_array($rs_attmpt, MYSQLI_NUM);

    if (isUsersexists($username)) {
        if ($data_attmpt ['attempt'] > 4) {

            $sql_lock = "UPDATE radius.radcheck SET locked = TRUE WHERE username='$username';";
            if ($conn->query($sql_lock) === TRUE) {


                return true;
            }

            return true;
        } else {

            $sql_attmp = "UPDATE radius.radcheck SET attempt =attempt+1 WHERE username='$username';";
            if ($conn->query($sql_attmp) === TRUE) {


                return true;
            }


            return false;
        }


    }

}


function attempt2($username)
{
    require 'config/dbc.php';
    $currntAttpmt = "SELECT attempt FROM  radcheck WHERE username='$username' ; ";

    $rs_attmpt = mysqli_query($conn, $currntAttpmt);

    $data_attmpt = mysqli_fetch_array($rs_attmpt, MYSQLI_NUM);

    if (isUsersexists($username)) {
        if ($data_attmpt ['0'] == 5) {
            $a="locked account " .$data_attmpt ['0'];
            $sql_lock = "UPDATE radius.radcheck SET locked = TRUE WHERE username='$username';";
            if ($conn->query($sql_lock) === TRUE) {


                return true;
            }

            return  $a;
        } else {

            $sql_attmp = "UPDATE radius.radcheck SET attempt =attempt+1 WHERE username='$username';";
            if ($conn->query($sql_attmp) === TRUE) {


                return true;
            }
            $a="locked not account " .$data_attmpt ['0'];


            return  $a;


        }


    }

}



echo " locked : ". attempt2('fadi') .$data_attmpt ['0'] ."<br>";
