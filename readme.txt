TNB Bank Web Services

mysqldump -p -u root -p radius > radius.sql
mysqldump -p -u root -p radius < radius.sql

10.60.60.10



                // header("location: http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$mobile&text=$mobile ");
           "http://91.240.148.34:13013/cgi-bin/sendsms?username=playsms&password=playsms&to=$mobile&text=$msg";
                //echo "<br>";












                $pass = GetPass($user);

                $msg = "TNBank, $user NEW Password: $pass";
                $url = "http://192.168.160.132/RemoteServices/crontab/sendSms.php";

                $SMS_URL = array(
                    'username' => "playsms",
                    'password' => "playsms",
                    'to' => "$mobile",
                    'text' => $msg
                );

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, count($SMS_URL));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($SMS_URL));

                $result = curl_exec($ch);

                curl_close($ch);
                 var_dump($result);
                echo http_build_query($SMS_URL) . "<br>";



$header = array("Content-Type:application/json", "Accept:application/json");

curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

// response of the POST request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseBody = json_decode($response);
curl_close($ch);