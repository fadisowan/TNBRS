<?php
/*
            crontab -e
            * * * * * php -q /var/www/html/daloradius-0.9-9/fadi2/crontab/cron.php

 */
require '/var/www/html/daloradius-0.9-9/RemoteServices/config/parm.php';
require '/var/www/html/daloradius-0.9-9/RemoteServices/config/mainClass.php';


if (file_exists(dirname(__DIR__) . '/crontab/users.txt')) {


    require '/var/www/html/daloradius-0.9-9/RemoteServices/config/dbc.php';

    $pick_files = file_get_contents(dirname(__DIR__) . '/crontab/users.txt');

    $picked_FormattedData = explode("\n", $pick_files);


    $userCount = 0;
    $iscomplete = false;
    foreach ($picked_FormattedData as $csvLine) {
        //list($user, $pass) = explode(",", $csvLine);
        $users = explode(",", $csvLine);

        //makeing sure user and pass are specified and are not empty
        //columns by chance
        isset($users[0]) ? $smsuser = $users[0] : $smsuser = "";

        if ((isset($users[0]) && (!empty($users[0])))
            &&
            ((isset($users[1]) && (!empty($users[1]))))
        ) {
            $user = trim($users[0]);
            $mobile = trim($users[1]);
            AddPickedUsers($user, $mobile);
            $pass = GetPass($user);
            $msg = "TNBank, $user NEW Password: $pass";
            //$url = "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$mobile&text=$msg";
            //$url="https://www.facebook.com";

          //  send_sms($mobile,$msg);
 send_sms($mobile,$msg) ;
        }

        $userCount++;

    }

} else {
    echo "No users file exists";
}

function send_sms($to, $msg ) {
    $uri = "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$to&text=$msg";
    $ch = curl_init();
    curl_setopt( $ch, $uri );
    $result = curl_exec( $ch );
    curl_close($ch);
    return $result;

}
