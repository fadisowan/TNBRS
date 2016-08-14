<?php
 
if (file_exists('users.txt')){


    $conn = new mysqli("localhost", "root", "", "radius");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $pick_files = file_get_contents("users.txt");

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

        echo "<pre>All Users Added Successfully</pre>";
        $userCount++;

    }

}else{
    echo "not file exists";
}




function AddPickedUsers($username, $password)
{
    // require 'config/dbc.php';
    $conn = new mysqli("localhost", "root", "", "radius");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
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

function isUsersexists($username)
{
    $conn = new mysqli("localhost", "root", "", "radius");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userexists = "SELECT * FROM  radius.radcheck WHERE username='$username'";
    $rs_userexists = mysqli_query($conn, $userexists);

    $data_userexists = mysqli_fetch_array($rs_userexists, MYSQLI_NUM);


    if ($data_userexists[0] > 1) {

        return true;
    } else {

        return false;
    }


}



