<?php
include 'db_conn.php';
include 'logger.php';
include 'GCM.php';
$logger = new Logger ();
$gcm = new GCM ();

$logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
$logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );

$logger->write ( "INFO :", "after post" );
$hospital_user_id = $_POST ['hospital_user_id'];
$hospital_id = $_POST ['hospital_id'];
$referring_doc_id = $_POST ['referring_doc_id'];

$logger->write ( "INFO :", "received post" . $hospital_user_id . "hospital_id" . $hospital_id . "referring_doc_id" . $referring_doc_id );


$query_check_before_map = "select * from referral_mapping where hospital_id='$hospital_id' and referring_doctor_id='$referring_doc_id'";
$result_chk = mysql_query ( $query_check_before_map );
if (mysql_num_rows ( $result_chk ) == 0) {
    $success = array (
            'status' => "Success",
            "msg" => "Something wrong.Please contact the administrator" 
    );
    echo json_encode ( $success );
} else {
    
    $delete_status = "delete from referral_mapping where hospital_id='$hospital_id' and referring_doctor_id='$referring_doc_id'";
    $result = mysql_query ( $delete_status );
    
    if ($result) {
        
        $success = array (
                'status' => "Success",
                "msg" => "Referring doctor mapping removed succesfully" 
        );
        echo json_encode ( $success );
    } else {
        $success = array (
                'status' => "Failure",
                "msg" => "Referring Doctor mapping could not be removed" 
        );
        echo json_encode ( $success );
    }
}

/*
 * function send_msg($mobile,$mobile_msg){
 *
 * $this->logger->write("INFO :","login with mobile".$mobile);
 * //$ch = curl_init();
 * //curl_setopt($ch,CURLOPT_VERBOSE,1);
 * $msg="";
 *
 * $msg=urlencode($mobile_msg);
 *
 *
 * $this->logger->write("INFO :","login with msg".$msg);
 * $url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;
 *
 * $this->logger->write("INFO :","login with url".$url);
 *
 * $ch = curl_init(); // setup a curl
 * curl_setopt($ch, CURLOPT_URL, $url); // set url to send to
 * curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
 * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo
 * curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails
 * curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
 * $output=curl_exec($ch);
 * //echo "output".$output;
 * curl_close($ch);
 * return $output;
 * }
 * function json($data){
 * if(is_array($data)){
 * return json_encode($data);
 * }
 * }
 */

?>
