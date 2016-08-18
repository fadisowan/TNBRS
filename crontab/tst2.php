<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/18/2016
 * Time: 10:55 AM
 */

$ch = curl_init();
// curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, "https://www.facebook.com/");
$output = curl_exec($ch);
curl_close($ch);

var_dump( $output);

