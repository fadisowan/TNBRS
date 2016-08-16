<?php
/*
            crontab -e
            * * * * * php -q /var/www/html/daloradius-0.9-9/fadi2/crontab/cron.php

 */

require dirname(__DIR__).'/config/mainClass.php';
if (file_exists(dirname(__DIR__).'/crontab/users.txt')){


    require dirname(__DIR__).'/config/dbc.php';

    $pick_files = file_get_contents(dirname(__DIR__).'/crontab/users.txt');

    $picked_FormattedData = explode("\n", $pick_files);


    $userCount = 0;
    $iscomplete=false;
    foreach ($picked_FormattedData as $csvLine) {
        //list($user, $pass) = explode(",", $csvLine);
        $users = explode(",", $csvLine);

        //makeing sure user and pass are specified and are not empty
        //columns by chance
        if ((isset($users[0]) && (!empty($users[0])))
            &&
            ((isset($users[1]) && (!empty($users[1]))))
        ) {
            $user = trim($users[0]);
            $pass = trim($users[1]);
            AddPickedUsers($user, $pass);

        }

        //echo "<pre>Users Added Successfully: $user </pre>";
        $userCount++;

    }

}else{
    echo "No users file exists";
}




function AddPickedUsers($username, $password)
{
    // require 'config/dbc.php';
    require dirname(__DIR__).'/config/dbc.php';
    if (isUsersexists($username)) {
        $txnStatus = "user already exists";
        return $txnStatus;
    } else {
        $tnxSuffix = "tnx_";

        // $pwd = genPass();
        $pwd = $password;
        $tnx_pwd = $password;
        $sql = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$username', 'Cleartext-Password', ':=','$pwd')";
        $sql2 = "INSERT INTO radius.radcheck (id, username, attribute, op, value) VALUES (0,'$tnxSuffix$username', 'Cleartext-Password', ':=','$tnx_pwd')";
        //if ($conn->query($sql) === TRUE) {
        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {

            $txnStatus = 'create';
            return $txnStatus;
        } else {

            $txnStatus = "user can't created";
            return $txnStatus;
        }
        $conn->close();
    }

}




