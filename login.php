<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//------------- Main IF POST-------------------------------------------------
require_once 'config/parm.php';
isset($_GET['username']) ? $userNamePost = trim($_GET['username']) : $username = "";
//isset($_GET['password']) ?$passwordPost =  $_GET['password'] : $password = "";
isset($_GET['password']) ? $password =  $_GET['password'] : $password = "";

if(isset($userNamePost) && isset($password) ){
    if(!empty($userNamePost)&&!empty($password)){
//-------------------------------------------------------------------------
        require_once  ('lib/nusoap.php');
        $client=new nusoap_client($GLOBALS['WSDL']);
        $error = $client->getError();
        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }
//-------------------------------------------------------------------------

          $result=$client->call("ValidateLoginPwd", array(
              "ValidateLoginUSR"=>"$userNamePost",
              "ValidateLoginPWD"=>"$password"
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
        }
        elseif($result=='create') {
            echo "<pre>New User Created : $userNamePost </pre>";
        } elseif($result=='suspend') {
            echo "<pre>User suspend : $userNamePost  </pre>";
        } elseif($result=='resetLoginPwd') {
            echo "<pre>Login Password reset successfully : $userNamePost </pre>";
        } elseif($result=='resetTnxPwd') {
            echo "<pre>transaction Password reset successfully : $userNamePost </pre>";


        } else{
            echo "<pre>$result $userNamePost   </pre>";
        }
    }
//------------- ENDIF FALUT-------------------------------------------------

}//end for POST
