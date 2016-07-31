<?php


require_once  'lib/nusoap.php';
require_once 'config/parm.php';



$username = trim($_REQUEST['username']);


function CreateUser($usernameCreate){
    require  'config/dbc.php';

$sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$usernameCreate'";
$rs = mysqli_query($conn,$sqlCHK);
$data = mysqli_fetch_array($rs, MYSQLI_NUM);

if($data[0] > 1) {
    $txnStatus ="user already exists";
  return $txnStatus;
}  else {
  $tnxSuffix= $GLOBALS['tnx_'];

    $pwd=genPass();
    $tnx_pwd=genPass();
    $sql = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$usernameCreate', 'Cleartext-Password', ':=','$pwd')";
$sql2 = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$tnxSuffix$usernameCreate', 'Cleartext-Password', ':=','$tnx_pwd')";
    //if ($conn->query($sql) === TRUE) {
    if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {

        $txnStatus= 'create';
        return $txnStatus;
    } else {

        $txnStatus= "user can't created";
        return $txnStatus;
    }
    $conn->close();
}

}

function SuspendUser($suspendName){

    require  'config/dbc.php';
    $sqlRadcheck = "SELECT * FROM  radius.radcheck WHERE username='$suspendName'";
    $rsRadcheck = mysqli_query($conn,$sqlRadcheck);
    $dataRadcheck = mysqli_fetch_array($rsRadcheck, MYSQLI_NUM);
    $sql = "SELECT * FROM radius.radusergroup    WHERE Username = '$suspendName'";
    //= "SELECT * FROM radius.radusergroup  where username = '$suspendName'";
    $rs = mysqli_query($conn, $sql);
    $tnxSuffix= $GLOBALS['tnx_'];
    $fUsername=$tnxSuffix.$suspendName;
if ($dataRadcheck[0] >1){
        if(mysqli_num_rows($rs)==0  ) {
        $sql = "INSERT INTO radius.radusergroup (username,groupname, priority) VALUES ('$suspendName', 'daloRADIUS-Disabled-Users', 0)";
             $sql2= "INSERT INTO radius.radusergroup (username,groupname, priority) VALUES ('$fUsername', 'daloRADIUS-Disabled-Users', 0)";

            //if ($conn->query($sql) === TRUE) {
        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE ) {

            $txnStatus= 'suspend';
            return $txnStatus;
        } else {

            $txnStatus= "not suspend";
            return $txnStatus;
        }
        $conn->close();

    }  else {


        $txnStatus ="user already suspended :";
        return $txnStatus;

    }

} else{
    $txnStatus= "sorry, This user does not exist to suspend!";
    return $txnStatus;

}


}

function resetLoginPwd ($LoginUserPwd){
    require  'config/dbc.php';

    $pwd=genPass();
    $sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$LoginUserPwd'";
    $rs = mysqli_query($conn,$sqlCHK);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

    if($data[0] > 1) {
        $sql =  "UPDATE radius.radcheck SET value = '$pwd'  WHERE username='$LoginUserPwd';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "resetLoginPwd";
            return $txnStatus;
        }
    }else

    {
        $txnStatus= 'username not found!';
        return $txnStatus;
    }
    $conn->close();
}

function resetTnxPwd ($TNXUserPwd){
    require  'config/dbc.php';
    $pwd=genPass();
    $tnxSuffix= $GLOBALS['tnx_'];
    $sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$tnxSuffix$TNXUserPwd'";
    $rs = mysqli_query($conn,$sqlCHK);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

    if($data[0] > 1) {
        $sql =  "UPDATE radius.radcheck SET value = '$pwd'  WHERE username='$tnxSuffix$TNXUserPwd'";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "resetTnxPwd";
            return $txnStatus;
        }
    }else

    {
        $txnStatus= 'transaction username not found!';
        return $txnStatus;
    }
    $conn->close();
}

function ValidateLoginPwd ($ValidateLoginUSR,$ValidateLoginPWD)
{

    require  'config/dbc.php';
    //$sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";
    $sqlSUSPEND = "SELECT
  radusergroup.username,
  radusergroup.groupname
FROM radusergroup
WHERE radusergroup.username = '$ValidateLoginUSR'
AND radusergroup.groupname = 'daloRADIUS-Disabled-Users'
       ";
    $rsSUSPEND = mysqli_query($conn,$sqlSUSPEND);
   // $dataSUSPEND = mysqli_fetch_array($rsSUSPEND, MYSQLI_NUM);
    if(mysqli_num_rows($rsSUSPEND)==0) {

        $sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";

        $rs = mysqli_query($conn,$sqlLogin);
        $data = mysqli_fetch_array($rs, MYSQLI_NUM);
        if($data[0] > 1) {


            ///check is first time login
            if (isFirstTime($ValidateLoginUSR)==1){
                //redirect("changepwd.php");
                $txnStatus ="first time login";

            }else{

                $txnStatus ="not first time login";
            }



            return $txnStatus;
        }  else {
            $txnStatus ="username or password invalid, try again";
            return $txnStatus;
        }

    }else{
        $txnStatus ="user suspended you can't login";

        return $txnStatus;
    }

}


function isFirstTime($username){
    require  'config/dbc.php';


    $Sql_isFirstTime = "SELECT username,firstLogin FROM radcheck where username='$username'";


    $rs = mysqli_query($conn, $Sql_isFirstTime);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);
    if ($data[1] == 1) {
        $rslt=true;
    } else {
         $rslt= false;
    }
    return $rslt;
}



function ChangePassword($ValidateLoginUSR,$ValidateLoginPWD,$ValidateLoginnewPWD){

    require  'config/dbc.php';

        $sqlLogin = "SELECT  * FROM radius.radcheck   WHERE username='$ValidateLoginUSR'  and value ='$ValidateLoginPWD'";

        $rs = mysqli_query($conn,$sqlLogin);
        $data = mysqli_fetch_array($rs, MYSQLI_NUM);
        if($data[0] > 1) {
// UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD'  WHERE username='$ValidateLoginUSR';
            $sql =  "UPDATE radius.radcheck SET value = '$ValidateLoginnewPWD'  WHERE username='$ValidateLoginUSR';";
            if ($conn->query($sql) === TRUE) {

                $txnStatus = "password change";
                return $txnStatus;
            }



        }  else {
            $txnStatus ="not login";
            return $txnStatus;
        }


}
function ForgetPassword ($forgetusername){
    require  'config/dbc.php';

    $pwd=genPass();
    $sqlCHK = "SELECT * FROM  radius.radcheck WHERE username='$forgetusername'";
    $rs = mysqli_query($conn,$sqlCHK);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

    if($data[0] > 1) {
        $sql =  "UPDATE radius.radcheck SET value = '$pwd'  WHERE username='$forgetusername';";
        if ($conn->query($sql) === TRUE) {

            $txnStatus = "Password Has been reset ";
            return $txnStatus;
        }
    }else

    {
        $txnStatus= 'username not found! to reset password';
        return $txnStatus;
    }
    $conn->close();
}

$server = new soap_server();
$server->configureWSDL("TNB Bank Web Serives","urn:Radius");
$server->register('CreateUser',array("usernameCreate" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#CreateUser");
$server->register('SuspendUser',array("suspendName" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#SuspendUser");
$server->register('resetLoginPwd',array("LoginPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#resetLoginPwd");
$server->register('resetTnxPwd',array("TNXUserPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#resetTnxPwd");
$server->register('ValidateLoginPwd',array("ValidateLoginPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#ValidateLoginPwd");
$server->register('ChangePassword',array("ChangePassword" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#ChangePassword");
$server->register('ForgetPassword',array("ForgetPassword" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#ForgetPassword");



$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

 $server->service($HTTP_RAW_POST_DATA);
