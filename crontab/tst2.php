<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/18/2016
 * Time: 10:55 AM
 */

send_sms("0599661094", "hello" );

function send_sms($to, $msg ) {
    $uri = "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$to&text=$msg";
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $uri );
    $output=  curl_exec( $ch );
    curl_close($ch);
    return $output;

}
