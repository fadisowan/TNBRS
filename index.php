<?php

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
            $txnStatus ="<br>user successfully login";
            isFirstTime($ValidateLoginUSR);
            echo  $txnStatus;
        }  else {
            $txnStatus ="<br>username or password invalid, try again";
            echo $txnStatus;
        }

    }else{
        $txnStatus ="user suspended you can't login";

        echo $txnStatus;
    }

}


function isFirstTime($username){
    require  'config/dbc.php';

    $Sql_isFirstTime = "SELECT username,firstLogin FROM radcheck where username='$username'";


    $rs = mysqli_query($conn, $Sql_isFirstTime);
    $data = mysqli_fetch_array($rs, MYSQLI_NUM);
    if ($data[1] == 1) {
        $txnStatus = "This Login is the first time ";



        echo $txnStatus;
    } else {
        $txnStatus = "<br>This Login is not first time ";
        echo $txnStatus;
    }
}




/*

$dbuser="fadi";
$dbpassword="pass";







function ChangePassword($_username,$_password,$_newpassword){


    if ($dbuser==$_username && $dbpassword==$_password){

        echo "login";
        echo "<br> new password: $_newpassword";
    }else
    {
        echo "fail";
    }
}
ChangePassword($_GET['username'],$_GET['password'],$_GET['newpassword']);


*/