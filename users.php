<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//action
//------------- Main IF POST-------------------------------------------------
require_once 'config/parm.php';
isset($_GET['username']) ? $userNamePost = trim(strip_tags($_GET['username'])) : $userNamePost = "";
//isset($_GET['password']) ?$passwordPost =  $_GET['password'] : $password = "";
isset($_GET['action']) ? $action =  trim(strip_tags($_GET['action'])) : $action = "";

if(isset($userNamePost) && isset($action) ){
    if(!empty($userNamePost)&&!empty($action)){
//-------------------------------------------------------------------------
        require_once  ('lib/nusoap.php');
        $client=new nusoap_client($GLOBALS['WSDL']);
        $error = $client->getError();
        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }
//-------------------------------------------------------------------------

switch ($action){
    case "create":
        $result=$client->call("CreateUser", array("usernameCreate"=>"$userNamePost"));
        break;
    case "suspend":

        $result=$client->call("SuspendUser", array("suspendName"=>"$userNamePost"));

        break;

    case "resetLoginPwd":
        $result=$client->call("resetLoginPwd", array("LoginPwd"=>"$userNamePost"));

        break;


    case "resetTnxPwd":
        $result=$client->call("resetTnxPwd", array("TNXUserPwd"=>"$userNamePost"));

        break;
    default:
        echo "<pre> please select correct operation </pre>";
        break;
}

    }else{
        echo "<pre>username can't be null or empty!</pre>";
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
