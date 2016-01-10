<?php

//Phpinfo();
error_reporting(0);
$ch = curl_init();
$msg=urlencode("Please register yourself in this app.Doctor Referral APP."); 
$url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=7276698941&source=HCHKIN&message=".$msg;
   
 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
 
    $output=curl_exec($ch);
    echo "output".$output;
    curl_close($ch);

?>

