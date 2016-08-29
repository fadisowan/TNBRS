<?php


require_once '/var/www/html/daloradius-0.9-9/RemoteServices/lib/nusoap.php';
require_once '/var/www/html/daloradius-0.9-9/RemoteServices/config/parm.php';
require_once '/var/www/html/daloradius-0.9-9/RemoteServices/config/mainClass.php';

$username = trim($_REQUEST['username']);


function CreateUser($usernameCreate)
{
    require 'config/dbc.php';
    $usernameCreate=mysqli_real_escape_string($conn,$usernameCreate);


    if (isUsersexists($usernameCreate)) {
        $txnStatus = "Account already exists";
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

            $txnStatus = "Account can't created";
            return $txnStatus;
        }
        $conn->close();
    }

}

function SuspendUser($suspendName)
{

    require 'config/dbc.php';
    $suspendName=mysqli_real_escape_string($conn,$suspendName);


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

function resetPwd($LoginUserPwd)
{
    require 'config/dbc.php';
    $LoginUserPwd=mysqli_real_escape_string($conn,$LoginUserPwd);

    $pwd = genPass();
    $pwd_tnx = genPass();
    $tnxSuffix = $GLOBALS['tnx_'];

    if (isUsersexists($LoginUserPwd)) {
        $sql = "UPDATE radius.radcheck SET value = '$pwd',locked=FALSE,firstLogin=TRUE ,attempt=0 WHERE username='$LoginUserPwd';";
        $sql2 = "UPDATE radius.radcheck SET value = '$pwd_tnx',locked=FALSE,firstLogin=TRUE ,attempt=0  WHERE username='$tnxSuffix$LoginUserPwd'";


        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {

            $txnStatus = "resetLoginPwd";
            return $txnStatus;
        }
    } else {
        $txnStatus = 'username not found!';
        return $txnStatus;
    }
    $conn->close();
}


function ValidateLogin($ValidateLoginUSR, $ValidateLoginPWD)
{

    require 'config/dbc.php';
    $ValidateLoginUSR=mysqli_real_escape_string($conn,$ValidateLoginUSR);
    $ValidateLoginPWD=mysqli_real_escape_string($conn,$ValidateLoginPWD);
    // $dataSUSPEND = mysqli_fetch_array($rsSUSPEND, MYSQLI_NUM);

    if (!isUsersexists($ValidateLoginUSR)) {
        $txnStatus = "username not found! to login";
        return $txnStatus;
    } else {
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
}


function ChangePassword($ValidateLoginUSR, $ValidateLoginPWD, $ValidateLoginnewPWD)
{
/*
    $ValidateLoginUSR=mysqli_real_escape_string($conn,$ValidateLoginUSR);

    $ValidateLoginnewPWD=mysqli_real_escape_string($conn,$ValidateLoginnewPWD);
*/

    if (!isUsersexists($ValidateLoginUSR)) {
        $txnStatus = "username not found! to Change Password";
        return $txnStatus;
    } else {
        if (!isLocked($ValidateLoginUSR)) {


            require 'config/dbc.php';

            $sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";

            $rs = mysqli_query($conn, $sqlLogin);
            $data = mysqli_fetch_array($rs, MYSQLI_NUM);


            if ($data[0] > 1) {
                $ValidateLoginUSR=mysqli_real_escape_string($conn,$ValidateLoginUSR);

                $ValidateLoginnewPWD=mysqli_real_escape_string($conn,$ValidateLoginnewPWD);
// UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD'  WHERE username='$ValidateLoginUSR';
                $sql = "UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD', firstLogin=FALSE  WHERE username='$ValidateLoginUSR';";
                if ($conn->query($sql) === TRUE) {

                    $txnStatus = "You change Password Successfully";
                    return $txnStatus;
                }


            } else {
                $txnStatus = "Old Password Wrong";
                return $txnStatus;
            }
        } else {
            $txnStatus = "You can't change password your account is locked";
            return $txnStatus;
        }
    }
}

function ForgetPassword($forgetusername)
{
    if (!isUsersexists($forgetusername)) {
        $txnStatus = 'username not found! to reset password';
        return $txnStatus;
    } else {
        require 'config/dbc.php';

       // $forgetusername=mysqli_real_escape_string($conn,$forgetusername);
        $pwd = genPass();
        $sql = "UPDATE radius.radcheck SET value = '$pwd',firstLogin = TRUE ,locked=FALSE,attempt=0 WHERE username='$forgetusername';";
        if ($conn->query($sql) === TRUE) {
            $mobile = GetMobile($forgetusername);
            $msg = "TNBank, $forgetusername NEW Password: $pwd";

            SendSMS(trim($mobile), urlencode($msg));
            $txnStatus = "Password reset";
            return $txnStatus;
        }

        $conn->close();
    }




}



$server = new soap_server();
$server->configureWSDL("TNB Bank Web Serives | Integrated Solutions ", "urn:Radius");
$server->register('CreateUser', array("usernameCreate" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#CreateUser");
$server->register('SuspendUser', array("suspendName" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#SuspendUser");
$server->register('resetPwd', array("LoginUserPwd" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#resetPwd");
$server->register('ValidateLogin', array("ValidateLogin" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ValidateLogin");
$server->register('ChangePassword', array("ChangePassword" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ChangePassword");
$server->register('ForgetPassword', array("forgetusername" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ForgetPassword");


$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

$server->service($HTTP_RAW_POST_DATA);
