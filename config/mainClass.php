<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/14/2016
 * Time: 1:08 PM
 */
/*ini_set('dispaly_errors',1);*/

function SendSMS($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';

    $MOBILE = GetMobile($username);
    $pass = GetPass($username);

    $msg = "TNBank NEW Password " . '"' . $pass . '"' . '<br>';

    echo $msg;


    $URL = "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$pass&text=$pass";
    // echo $URL."<br>";

    /*
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output = curl_exec($ch);
    var_dump($output);
    curl_close($ch);
*/


}
function AddPickedUsers($username, $mobile)
{
    // require 'config/dbc.php';
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';
    if (isUsersexists($username)) {
        $txnStatus = "user already exists";
        return $txnStatus;
    } else {
        $tnxSuffix = "tnx_";

        $pwd = genPass();

        $tnx_pwd = genPass();
        $sql = "INSERT INTO radius.radcheck (id, username, attribute, op, value,mobile) VALUES (0,'$username', 'Cleartext-Password', ':=','$pwd','$mobile')";
        $sql2 = "INSERT INTO radius.radcheck (id, username, attribute, op, value,mobile) VALUES (0,'$tnxSuffix$username', 'Cleartext-Password', ':=','$tnx_pwd','$mobile')";
        //if ($conn->query($sql) === TRUE) {
        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {

            $txnStatus = 'create';

            return $txnStatus;
        } else {

            $txnStatus = "user can't created";
            return $txnStatus;
        }
        $conn->close();

    }

}
function GetMobile($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';


    $Sql_getMobile = "SELECT  mobile FROM   radius.radcheck WHERE username='$username'";
    $rs_getMobile = mysqli_query($conn, $Sql_getMobile);

    $data_getMobile = mysqli_fetch_array($rs_getMobile, MYSQLI_NUM);


    if ($data_getMobile[0] > 1) {

        return $data_getMobile[0];
    }
}
function GetPass($username)
{

    if (isUsersexists($username)) {
        require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';


        $Sql_getPass = mysqli_fetch_assoc(mysqli_query($conn, "SELECT value FROM radcheck where username='$username'"));
        $getPass = $Sql_getPass['value'];
        return $getPass;
    } else {
        return false;
    }

}
function isUsersexists($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';

    $userexists = "SELECT * FROM  radius.radcheck WHERE username='$username'";
    $rs_userexists = mysqli_query($conn, $userexists);

    $data_userexists = mysqli_fetch_array($rs_userexists, MYSQLI_NUM);


    if ($data_userexists[0] > 0) {

        return true;
    } else {

        return false;
    }


}
function isLocked($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';

    $sql_locked = " SELECT  locked FROM  radcheck WHERE username='$username';
 ";


    $rs_locked = mysqli_query($conn, $sql_locked);
    $data_locked = mysqli_fetch_array($rs_locked, MYSQLI_NUM);

    if ($data_locked [0] == true) {

        return true;
    } else {
        return false;
    }
}
function isUserSuspended($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';

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
function attempt($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';
    $currntAttpmt = "SELECT attempt FROM  radcheck WHERE username='$username' ; ";

    $rs_attmpt = mysqli_query($conn, $currntAttpmt);

    $data_attmpt = mysqli_fetch_array($rs_attmpt, MYSQLI_NUM);

    if (isUsersexists($username)) {
        if ($data_attmpt ['0'] >= 4) {//= 5 attmpt

            $sql_lock = "UPDATE radius.radcheck SET  locked = TRUE WHERE username='$username';";
            if ($conn->query($sql_lock) === TRUE) {


                return true;
            }

            //return true;
        } else {

            $sql_attmp = "UPDATE radius.radcheck SET attempt =attempt+1 WHERE username='$username';";
            if ($conn->query($sql_attmp) === TRUE) {


                return true;
            }


            //return false;
        }


    }

}
function isFirstTime($username)
{
    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';


    $Sql_isFirstTime = "SELECT username,firstLogin FROM radcheck where username='$username'";


    $rs = mysqli_query($conn, $Sql_isFirstTime);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);
    if ($data[1] == 1) {
        $rslt = true;
    } else {
        $rslt = false;
    }
    return $rslt;
}







