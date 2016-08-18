<?php
/**
 * Created by PhpStorm.
 * User: fadi
 * Date: 4/10/2016
 * Time: 2:00 PM
 */
$GLOBALS['WSDL']="http://192.168.160.132/RemoteServices/services.php?wsdl";
//GLOBALS['WSDL']="http://192.168.160.132/fadi2/services.php?wsdl";
$GLOBALS['tnx_']='tnx_';
$GLOBALS['SMS_IP']='http://91.240.148.34/';
$GLOBALS['PICK_USERS_FILE']='users.txt';


function genPass($len = 8) {
    $charPool = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $length = strlen($charPool) - 1;
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $length);
        $pass[] = $charPool[$n];
    }
    return implode($pass);
}