<?php
//Phone Number:8824166322
//IOS ID:

$file="ck.pem";
$pwd="password";
$server="ssl://gateway.push.apple.com:2195";
//$server="gateway.sandbox.push.apple.com";
$aplContent="Test Apple12345";
$taken="0f943e664b7e6c3fd3e1f1a6147b74fbc97b2d32559116001e67772a2f8119f6";
//$tokenArr=array();
//array_push($tokenArr,$taken);

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', $file);
stream_context_set_option($ctx, 'ssl', 'passphrase', $pwd);

$apns_fp = stream_socket_client($server, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

if ($apns_fp) {


    $body['aps'] = $aplContent;

    $payload = json_encode($body);

    $msg = '';
    //foreach ($tokenArr as $token) {
        $msg = chr(0) . pack('n', 32) . pack('H*', $taken) . pack('n', strlen($payload)) . $payload;
   // }

    $result = fwrite($apns_fp, $msg, strlen($msg));
    
    echo $result;

    /* if (!$result)
        echo array('errorNo' => 46);
    else
        echo array('errorNo' => 44); */
} /* else {
    echo array('errorNo' => 30, 'error' => $errstr);
} */


?>