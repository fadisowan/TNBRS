<?php


require_once 'lib/nusoap.php';
require_once 'config/parm.php';


$username = trim($_REQUEST['username']);


function CreateUser($usernameCreate)
{
    require 'config/dbc.php';

    if (isUsersexists($usernameCreate)) {
        $txnStatus = "user already exists";
        return $txnStatus;
    } else {
        $tnxSuffix = $GLOBALS['tnx_'];

        $pwd = genPass();
        $tnx_pwd = genPass();
        $sql = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$usernameCreate', 'Cleartext-Password', ':=','$pwd')";
        $sql2 = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$tnxSuffix$usernameCreate', 'Cleartext-Password', ':=','$tnx_pwd')";
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

function SuspendUser($suspendName)
{

    require 'config/dbc.php';

    $tnxSuffix = $GLOBALS['tnx_'];
    $fUsername = $tnxSuffix . $suspendName;
    if (isUsersexists($suspendName)) {
        if (!isUserSuspended($suspendName)) {
            $sql = "INSERT INTO radius.radusergroup (username,groupname, priority) VALUES ('$suspendName', 'daloRADIUS-Disabled-Users', 0)";
            $sql2 = "INSERT INTO radius.radusergroup (username,groupname, priority) VALUES ('$fUsername', 'daloRADIUS-Disabled-Users', 0)";

            //if ($conn->query($sql) === TRUE) {
            if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {

                $txnStatus = 'suspend';
                return $txnStatus;
            } else {

                $txnStatus = "not suspend";
                return $txnStatus;
            }
            $conn->close();

        } else {


            $txnStatus = "user already suspended :";
            return $txnStatus;

        }

    } else {
        $txnStatus = "sorry, This user does not exist to suspend!";
        return $txnStatus;

    }


}

function resetLoginPwd($LoginUserPwd)
{
    require 'config/dbc.php';

    $pwd = genPass();


    if (isUsersexists($LoginUserPwd)) {
        $sql = "UPDATE radius.radcheck SET value = '$pwd',locked=FALSE,firstLogin=TRUE ,attempt=0 WHERE username='$LoginUserPwd';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "resetLoginPwd";
            return $txnStatus;
        }
    } else {
        $txnStatus = 'username not found!';
        return $txnStatus;
    }
    $conn->close();
}

function resetTnxPwd($TNXUserPwd)
{
    require 'config/dbc.php';
    $pwd = genPass();
    $tnxSuffix = $GLOBALS['tnx_'];


    if (isUsersexists($TNXUserPwd)) {
        $sql = "UPDATE radius.radcheck SET value = '$pwd',locked=FALSE,firstLogin=TRUE ,attempt=0  WHERE username='$tnxSuffix$TNXUserPwd'";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "resetTnxPwd";
            return $txnStatus;
        }
    } else {
        $txnStatus = 'transaction username not found!';
        return $txnStatus;
    }
    $conn->close();
}

function ValidateLogin($ValidateLoginUSR, $ValidateLoginPWD)
{

    require 'config/dbc.php';

    // $dataSUSPEND = mysqli_fetch_array($rsSUSPEND, MYSQLI_NUM);


    if (!isUserSuspended($ValidateLoginUSR)) {
//start
        if (!isLocked($ValidateLoginUSR)) {
            $sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";

            $rs = mysqli_query($conn, $sqlLogin);
            $data = mysqli_fetch_array($rs, MYSQLI_NUM);
            if ($data[0] > 1) {

                if (isFirstTime($ValidateLoginUSR) == 1) {


                    $txnStatus = "first time login";

                } else {

                    $txnStatus = "not first time login";
                }


                return $txnStatus;
            } else {

                attempt($ValidateLoginUSR);
                $txnStatus = "username or password invalid, try again";
                return $txnStatus;


            }
        } else {

            $txnStatus = "Account is Locked";

            return $txnStatus;
        }
    } else {
        $txnStatus = "user suspended you can't login";

        return $txnStatus;
    }

}

function attempt($username)
{
    require 'config/dbc.php';
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
    require 'config/dbc.php';


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

function isLocked($username)
{
    require 'config/dbc.php';

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

function ChangePassword($ValidateLoginUSR, $ValidateLoginPWD, $ValidateLoginnewPWD)
{


    if(!isLocked($ValidateLoginUSR)){


    require 'config/dbc.php';

    $sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";

    $rs = mysqli_query($conn, $sqlLogin);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);
    if ($data[0] > 1) {
// UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD'  WHERE username='$ValidateLoginUSR';
        $sql = "UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD', firstLogin=FALSE  WHERE username='$ValidateLoginUSR';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "password change";
            return $txnStatus;
        }


    } else {
        $txnStatus = "not login";
        return $txnStatus;
    }
    }else{
        $txnStatus = "You can't change password your account is locked";
        return $txnStatus;
    }

}

function ForgetPassword($forgetusername)
{
    require 'config/dbc.php';

    $pwd = genPass();
    $sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$forgetusername'";
    $rs = mysqli_query($conn, $sqlCHK);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

    if ($data[0] > 1) {
        $sql = "UPDATE radius.radcheck SET value = '$pwd',firstLogin = TRUE ,locked=FALSE WHERE username='$forgetusername';";
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

$server = new soap_server();
$server->configureWSDL("TNB Bank Web Serives", "urn:Radius");
$server->register('CreateUser', array("usernameCreate" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#CreateUser");
$server->register('SuspendUser', array("suspendName" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#SuspendUser");
$server->register('resetLoginPwd', array("LoginPwd" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#resetLoginPwd");
$server->register('resetTnxPwd', array("TNXUserPwd" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#resetTnxPwd");
$server->register('ValidateLogin', array("ValidateLogin" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ValidateLogin");
$server->register('ChangePassword', array("ChangePassword" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ChangePassword");
$server->register('ForgetPassword', array("ForgetPassword" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ForgetPassword");


$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

$server->service($HTTP_RAW_POST_DATA);
