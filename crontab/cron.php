<?php
/*
            crontab -e
            * * * * * php -q /var/www/html/daloradius-0.9-9/fadi2/crontab/cron.php

 */
//ini_set('dispaly_errors',1);
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
            // $user = ctype_alnum (strip_tags(trim($users[0])));


            $username = strip_tags(trim(($users[0])));
            $mobile = strip_tags(trim($users[1]));

            if (!isUsersexists($username)) {

                AddPickedUsers($username, $mobile);
                $pass = GetPass($username);
                $msg = "TNBank, $username NEW Password: $pass";


                SendSMS($mobile, urlencode($msg));
                SendEmail("fadi.sowan@gmail.com",$msg);
            }
        }

        $userCount++;

    }

} else {
    echo "No users file exists";
}

