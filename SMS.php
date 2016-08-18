<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//------------- Main IF POST-------------------------------------------------
require_once 'config/parm.php';
//git username /password
isset($_GET['username']) ? $username = strip_tags($_GET['username']) : $username = "";


if(isset($username)  ){
    if(!empty($username)){
//-------------------------------------------------------------------------
        require_once  ('lib/nusoap.php');
        $client=new nusoap_client($GLOBALS['WSDL']);
        $error = $client->getError();
        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }
//-------------------------------------------------------------------------

        $result=$client->call("forgeteSMS", array(
            "username"=>"$username"

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
            echo "<pre>$result $username   </pre>";
        }
    }
//------------- ENDIF FALUT-------------------------------------------------

}//end for POST
