<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//------------- Main IF POST-------------------------------------------------
require_once 'config/parm.php';
//git username /password
isset($_GET['username']) ? $userNamePost = strip_tags($_GET['username']) : $userNamePost = "";
//isset($_GET['password']) ?$passwordPost =  $_GET['password'] : $password = "";
isset($_GET['password']) ? $passwordPOST =  $_GET['password'] : $passwordPOST = "";
isset($_GET['newpassword']) ? $newpasswordPOST =  $_GET['newpassword'] : $newpasswordPOST = "";

if(isset($userNamePost) && isset($passwordPOST) ){
    if(!empty($userNamePost)&&!empty($passwordPOST)){
//-------------------------------------------------------------------------
        require_once  ('lib/nusoap.php');
        $client=new nusoap_client($GLOBALS['WSDL']);
        $error = $client->getError();
        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }
//-------------------------------------------------------------------------

        $result=$client->call("ChangePassword", array(
            "ValidateLoginUSR"=>"$userNamePost",
            "ValidateLoginPWD"=>"$passwordPOST",
            "ValidateLoginnewPWD"=>"$newpasswordPOST",

        ));


    }else{
        echo "<pre>username or password can't be null or empty!</pre>";
    }
//------------- IF FALUT-------------------------------------------------
    $par='01';
    if ($client->fault) {
        echo "<h2>Fault</h2><pre>";
        print_r($result);
        echo "</pre>";
    }
    else {
        $error = $client->getError();
        if ($error) {
            echo "<h2>Error</h2><pre>" . $error . "</pre>";


        } else{
            echo "<pre>$result $userNamePost   </pre>";
        }
    }
//------------- ENDIF FALUT-------------------------------------------------

}//end for POST
