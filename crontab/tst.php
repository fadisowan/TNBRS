<?php
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