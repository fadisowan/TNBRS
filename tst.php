<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/2/2016
 * Time: 11:02 AM
 */require_once 'lib/nusoap.php';
require_once 'config/parm.php';

function ForgetPassword($forgetusername)
{
    require 'config/dbc.php';

    $pwd = genPass();
    $sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$forgetusername'";
    $rs = mysqli_query($conn, $sqlCHK);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

    if ($data[0] > 1) {
        $sql = "UPDATE radius.radcheck SET value = '$pwd',firstLogin = TRUE  WHERE username='$forgetusername';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "Password su reset ";
            return $txnStatus;
        }
    } else {
        $txnStatus = 'username not found! to reset password';
        return $txnStatus;
    }

    $conn->close();

}
function FlagReset($username)
{
    require 'config/dbc.php';

    $sql_flag = "UPDATE radius.radcheck SET firstLogin = TRUE  WHERE username='$username'";



    $rs_flag = mysqli_query($conn, $sql_flag);
    $data = mysqli_fetch_array($rs_flag, MYSQLI_NUM);
    if ($conn->query($sql_flag) === TRUE) {
       return true;
    }


}

echo ForgetPassword("fadi");