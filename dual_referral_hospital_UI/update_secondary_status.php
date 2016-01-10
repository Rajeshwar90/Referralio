<?php

session_start();
if(!isset($_SESSION['hospital_id_SESSION']))
{
    header("Location: hospital_login.php");
    die();
}

$hospital_id = $_SESSION ['hospital_id_SESSION'];
$hospital_name = $_SESSION ['hospital_name_SESSION'];

include 'db_conn.php';
include 'Email_sender.php';
include 'logger.php';
include 'GCM.php';
$logger = new Logger();
$gcm= new GCM();
$email_sender=new EMAIL_SENDER();

$logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
$logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );



$logger->write("INFO :","before post");
$pat_id = $_POST['pat_id'];
$status = $_POST['status'];

$logger->write("INFO :","received post");

/*for sending SMS to refering doctor regarding the patient */
$query_pat_referring="select tmp.*,sd.Doctor_mobile_number as Ref_mob_number,sd.Doctor_Title as Ref_Title,sd.Doctor_name as Ref_name,sd.Doctor_email as Ref_email from doctor_stub sd inner join (SELECT ps.patient_thread_id,ps.Patient_Name ,ps.reg_by_doc,ps.doc_ref_id,ds.Doctor_Title as Reg_Title,ds.Doctor_name as Reg_name,ds.Doctor_mobile_number as Reg_mobnumber,ds.Doctor_email as Reg_email FROM `patient_stub` ps inner join doctor_stub ds on ps.reg_by_doc=ds.Doctor_serial_id where ps.patient_thread_id= '$pat_id')as tmp on tmp.doc_ref_id=sd.Doctor_serial_id";

$logger->write("INFO for query:",$query_pat_referring);
$res_pat_referring=mysql_query($query_pat_referring);
$row_pat_referring=mysql_fetch_assoc($res_pat_referring);

$reg_mob_number=$row_pat_referring['Reg_mobnumber'];
$reg_title=$row_pat_referring['Reg_Title'];
$reg_name=$row_pat_referring['Reg_name'];
$reg_email=$row_pat_referring['Reg_email'];
$pat_name=$row_pat_referring['Patient_Name'];
$ref_mob_number=$row_pat_referring['Ref_mob_number'];
$ref_title=$row_pat_referring['Ref_Title'];
$ref_name=$row_pat_referring['Ref_name'];
$ref_email=$row_pat_referring['Ref_email'];

//end of getting data

$sms_msg_referring_doc=$hospital_name. " - Dear ".$reg_title.$reg_name.", current status for patient ".$pat_name." : ".$status;

$sms_msg_referred_doc=$hospital_name. " - Dear ".$ref_title.$ref_name.", current status for patient ".$pat_name." : ".$status;

$update_status="update patient_stub set secondary_status='$status' where Patient_thread_id='$pat_id'";
$result=mysql_query($update_status);


if ($result) {
    
    send_msg($reg_mob_number,$sms_msg_referring_doc);
    send_msg($ref_mob_number,$sms_msg_referred_doc);
    
    if($reg_email!=''){
        $email_sender->send_email($reg_email,$sms_msg_referring_doc);
    }
    if($ref_email!=''){
        $email_sender->send_email($ref_email,$sms_msg_referred_doc);
    }
		
	$success = array('status' => "Success", "msg" => "Patient status updated successfully");
	echo json_encode($success);
	
	
}
else{
	$success = array('status' => "Failure", "msg" => "Patient status could not be updated");
	echo json_encode($success);
}


function send_msg($mobile,$mobile_msg){
		    
			//$logger->write("INFO :","login with mobile".$mobile);
		    
			$msg="";
						
			$msg=urlencode($mobile_msg);
			  
			
			
			//$logger->write("INFO :","login with msg".$msg);
			$url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;
			
			//$logger->write("INFO :","login with url".$url);
            
			$ch = curl_init();  // setup a curl
			curl_setopt($ch, CURLOPT_URL, $url);  // set url to send to
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
			$output=curl_exec($ch);
           //echo "output".$output;
			curl_close($ch);
			//return $output;
		}


?>
