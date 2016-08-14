<?php
require 'config/mainClass.php';
function SendSMS($username){

    require 'config/dbc.php';


    if (isUsersexists($username)) {
        //$txnStatus = "Account already exists";
       //  GetMobile($username);
        return GetMobile($username);
    } else {
        $txnStatus = "Account not exists to Send SMS";
        return $txnStatus;

    }

}



echo SendSMS('fadi2');