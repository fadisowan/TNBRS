<?php

/*
function getUrl()
{
    $ch = curl_init();

// set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, "http://192.168.160.132/RemoteServices/services.php?wsdl");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    var_dump(curl_exec($ch));
    curl_close($ch);
}
getUrl();




 $url= "http://192.168.160.132/RemoteServices/crontab/sendSms.php";

$fields = array(
    'username'      => "playsms",
    'password'      => "playsms",
    'to'    => "iZycon",
    'text'      => 8443223
);

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

$result = curl_exec($ch);

curl_close($ch);

var_dump($result);

*/

function send_sms($to, $text ) {

    $uri =  "http://91.240.148.34:13013/cgi-bin/sendsms?";
   // $uri =               "http://192.168.160.132/RemoteServices/crontab/sendSms.php";
    // post string (phone number format= +15554443333 ), case matters
    $SMS_URL = array(
        'username' => "playsms",
        'password' => "playsms",
        'to' => "$to",
        'text' => $text
    );


    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, $uri );
    curl_setopt( $ch, CURLOPT_POST, 4); // number of fields
    curl_setopt($ch, CURLOPT_POST, count($SMS_URL));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($SMS_URL));
    curl_setopt( $ch, CURLOPT_POST, 4);
     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // don't echo

    $result = curl_exec( $ch );
    curl_close($ch);


    return $result;

}
