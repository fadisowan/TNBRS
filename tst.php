<?php

//ini_set('dispaly_errors',1);
require 'config/mainClass.php';
require 'config/parm.php';

 function SendSMS($username)
 {
     require 'config/dbc.php';
    //$MOBILE = "";


    if (isUsersexists($username)) {
       // $txnStatus = "Account already exists";

        $MOBILE = GetMobile($username);
        $pass=GetPass($username);
        $url="http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$MOBILE&text=$pass";

        ob_start(); // ensures anything dumped out will be caught

// do stuff here

// clear out the output buffer
        while (ob_get_status())
        {
            ob_end_clean();
        }

// no redirect
        header( "Location: $url" );
   } else {
       $txnStatus = "Account not exists to Send SMS";
        return $txnStatus;

     }

 }



isset($_GET['username']) ? $username = $_GET['username'] : $username = "";



if (!empty($username)) {

    $url=  SendSMS($username);
    //  header('location: $url');

    echo $url ;
} else {
    echo "no pass";
}