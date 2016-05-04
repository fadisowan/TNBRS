<?php

require_once  'lib/nusoap.php';
require_once 'config/parm.php';
 
$username = trim($_REQUEST['username']);
///------------ add New User Function------------////
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
///------------ End add New User Function------------////
///------------ Suspend User Function------------////

function SuspendUser($suspendName){

    require  'config/dbc.php';
    $sqlRadcheck = "SELECT * FROM  radius.radcheck WHERE username='$suspendName'";
    $rsRadcheck = mysqli_query($conn,$sqlRadcheck);
    $dataRadcheck = mysqli_fetch_array($rsRadcheck, MYSQLI_NUM);
    $sql = "SELECT * FROM radius.radusergroup    WHERE Username = '$suspendName'";
    //= "SELECT * FROM radius.radusergroup  where username = '$suspendName'";
    $rs = mysqli_query($conn, $sql);
if ($dataRadcheck[0] >1){
        if(mysqli_num_rows($rs)==0  ) {
        $sql = "INSERT INTO radius.radusergroup (username,groupname, priority) VALUES ('$suspendName', 'daloRADIUS-Disabled-Users', 0)";
        //if ($conn->query($sql) === TRUE) {
        if ($conn->query($sql) === TRUE ) {

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

///------------ End Suspend User Function------------////

///------------ Reset Login Password User Function------------////
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

///------------ end Reset Login Password User Function------------////
///------------ Reset Transaction Password User Function------------////
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


///------------ Reset Transaction Password User Function------------////
///------------ Reset Transaction Password User Function------------////
function ValidateLoginPwd ($ValidateLoginUSR,$ValidateLoginPWD)
{

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
function ValidateTnxPwd ($ValidateTnxPwd)
{

}



$server = new soap_server();
$server->configureWSDL("TNB Bank Web Serives","urn:Radius");
$server->register('CreateUser',array("usernameCreate" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#CreateUser");
$server->register('SuspendUser',array("suspendName" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#SuspendUser");
$server->register('resetLoginPwd',array("LoginPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#resetLoginPwd");
$server->register('resetTnxPwd',array("TNXUserPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#resetTnxPwd");
$server->register('ValidateLoginPwd',array("ValidateLoginPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#ValidateLoginPwd");
$server->register('ValidateTnxPwd',array("ValidateTnxPwd" => "xsd:string"),array("return" => "xsd:string"),"urn:Radius","urn:Radius#ValidateTnxPwd");




$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

 $server->service($HTTP_RAW_POST_DATA);
