<?php


require_once '/var/www/html/daloradius-0.9-9/RemoteServices/lib/nusoap.php';
require_once '/var/www/html/daloradius-0.9-9/RemoteServices/config/parm.php';
require '/var/www/html/daloradius-0.9-9/RemoteServices/config/mainClass.php';

$username = trim($_REQUEST['username']);


function CreateUser($usernameCreate)
{
    require 'config/dbc.php';

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

function ForgetPassword($username)
{
    if (!isUsersexists($username)) {
        $txnStatus = 'username not found! to reset password';
        return $txnStatus;
    } else {
        require 'config/dbc.php';

        $pwd = genPass();
        $sql = "UPDATE radius.radcheck SET value = '$pwd',firstLogin = TRUE ,locked=FALSE,attempt=0 WHERE username='$username';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "Password reset";
            return $txnStatus;
        }
        $mobile = GetMobile($username);
        $msg = "TNBank, $username NEW Password: $pwd";
        $uri = "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$mobile&text=$msg";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_exec($ch);
        curl_close($ch);

        $conn->close();
    }
}


$server = new soap_server();
$server->configureWSDL("TNB Bank Web Serives | Integrated Solutions ", "urn:Radius");
$server->register('CreateUser', array("usernameCreate" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#CreateUser");
$server->register('SuspendUser', array("suspendName" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#SuspendUser");
$server->register('resetLoginPwd', array("LoginPwd" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#resetLoginPwd");
$server->register('resetTnxPwd', array("TNXUserPwd" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#resetTnxPwd");
$server->register('ValidateLogin', array("ValidateLogin" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ValidateLogin");
$server->register('ChangePassword', array("ChangePassword" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ChangePassword");
$server->register('ForgetPassword', array("ForgetPassword" => "xsd:string"), array("return" => "xsd:string"), "urn:Radius", "urn:Radius#ForgetPassword");


$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

$server->service($HTTP_RAW_POST_DATA);
