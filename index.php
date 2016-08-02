<?php
/*
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


*/

