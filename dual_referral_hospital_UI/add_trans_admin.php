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
include 'logger.php';
include 'GCM.php';
include 'Email_sender.php';
$logger = new Logger();
$gcm= new GCM();
$email_sender=new EMAIL_SENDER();

$logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
$logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );

$doctor_name="";
$doc_mob_number="";
$doc_email="";
$doc_title="";

$doc_ref_id_name="";
$doc_ref_mobile_number="";
$doc_ref_email="";
$doc_ref_title="";

$gcm_id="";
$logger->write("INFO :","before post");
$pat_name = $_POST['pat_name'];
$pat_mobile = $_POST['pat_mobile'];
$pat_age = $_POST['pat_age'];
$pat_gender = $_POST['pat_gender'];
$pat_loc = $_POST['pat_loc'];
//$hos_doc_Id = $_POST['hos_doc_Id'];
//$ref_doc_id = $_POST['ref_doc_id'];
$hos_doc_Id=$_POST['ref_doc_id'];
$ref_doc_id=$_POST['hos_doc_Id'];
$pat_notes = $_POST['pat_notes'];
$hos_id = $_POST['hos_id'];


$logger->write("INFO :","received post"."hos_doc_Id==>".$hos_doc_Id."ref_doc_id==>".$ref_doc_id);

/* $query_get_doc_name="select * from doctor_stub where Doctor_serial_id IN ($hos_doc_Id,$ref_doc_id)";
$result_doc_name=mysql_query($query_get_doc_name);




$cnt=0;
while($row_name=mysql_fetch_assoc($result_doc_name)){
	if($cnt==0){ 
		$doctor_name=$row_name['Doctor_name'];
		$doc_mob_number=$row_name['Doctor_mobile_number'];
		$doc_email=$row_name['Doctor_email'];
		$doc_title=$row_name['Doctor_Title'];
	}
	else{
		$doc_ref_id_name=$row_name['Doctor_name'];
		$doc_ref_mobile_number=$row_name['Doctor_mobile_number'];
		$doc_ref_email=$row_name['Doctor_email'];
		$doc_ref_title=$row_name['Doctor_Title'];
	}
	$cnt++;
} */

$query_get_doc1="select * from doctor_stub where Doctor_serial_id ='$hos_doc_Id'";
$result_doc1=mysql_query($query_get_doc1);
while($row_name=mysql_fetch_assoc($result_doc1)){
    $doctor_name=$row_name['Doctor_name'];
    $doc_mob_number=$row_name['Doctor_mobile_number'];
    $doc_email=$row_name['Doctor_email'];
    $doc_title=$row_name['Doctor_Title'];
}


$query_get_doc2="select * from doctor_stub where Doctor_serial_id ='$ref_doc_id'";
$result_doc2=mysql_query($query_get_doc2);
while($row_name2=mysql_fetch_assoc($result_doc2)){
    $doc_ref_id_name=$row_name2['Doctor_name'];
    $doc_ref_mobile_number=$row_name2['Doctor_mobile_number'];
    $doc_ref_email=$row_name2['Doctor_email'];
    $doc_ref_title=$row_name2['Doctor_Title'];
}



$logger->write("INFO :","received name and mobile number");

$sql_gcm=mysql_query("select gcm_regid from gcm_users gs where gs.mob_number='$doc_ref_mobile_number'");
while($row_gcm=mysql_fetch_assoc($sql_gcm)){
	$gcm_id=$result_sql_gcm['gcm_regid'];
	
}

$logger->write("INFO :","login gcm_id".$gcm_id);


$sql_refer_in_cnt=mysql_query("select count(*) as cnt from patient_stub where doc_ref_id='$ref_doc_id' and refer_in_view_flag=0 and reg_by_doc='$hos_doc_Id'");
$result_refer_in = mysql_fetch_array($sql_refer_in_cnt,MYSQL_ASSOC);
$cnt_refer_in=$result_refer_in['cnt'];

$logger->write("INFO :","refer in count".$cnt_refer_in);

$created_by="Admin";

$sql_msg="insert into patient_stub (Patient_Name, Patient_Age, Patient_Gender, Patient_Location, Patient_mobile_number, Patient_issue_notes, Reg_by_doc, Patient_defined_notes, doc_ref_id,hospital_id_transaction,created_by) values('$pat_name','$pat_age','$pat_gender','$pat_loc','$pat_mobile','$pat_notes','$hos_doc_Id','$pat_notes','$ref_doc_id',$hos_id,'$created_by')";

$logger->write("INFO :","sql message".$sql_msg);

$sql_insert = mysql_query ( "insert into patient_stub (Patient_Name, Patient_Age, Patient_Gender, Patient_Location, Patient_mobile_number, Patient_issue_notes, Reg_by_doc, Patient_defined_notes, doc_ref_id,hospital_id_transaction,created_by) values('$pat_name','$pat_age','$pat_gender','$pat_loc','$pat_mobile','$pat_notes','$hos_doc_Id','$pat_notes','$ref_doc_id',$hos_id,'$created_by')" );

$logger->write ( "INFO :", "patient inserted" );

//changes for referral mapping
$hospital_user_id="";
$mapping_id="";
$patient_auto_id=mysql_insert_id();

$logger->write ( "INFO :", "patient inserted id =>".$patient_auto_id );
//query to get the hospital for the hospital doctor
$query_get_hospital="select * from hospital_refer_out_doctor_stub where doctor_stub_id='$ref_doc_id'";
$res_get_hospital=mysql_query($query_get_hospital);
if(mysql_num_rows($res_get_hospital)==1)
{
    $logger->write ( "INFO :", "get count hospital_id =>".mysql_num_rows($res_get_hospital) );
    $hospital_user_available=1;
    $row_get_hospital=mysql_fetch_assoc($res_get_hospital);
    $hospital_id=$row_get_hospital['hospital_id'];
    
    $logger->write ( "INFO :", "hospital_id =>".$hospital_id );

    //query to check for any hospital_user for this referring doctor
    $query_get_referral_map="select * from referral_mapping where hospital_id='$hospital_id' and referring_doctor_id='$hos_doc_Id'";
    $logger->write ( "INFO :", "query_get_referral_map =>".$query_get_referral_map );
    $res_referral_map=mysql_query($query_get_referral_map);
    $logger->write ( "INFO :", "rows ****** =>".mysql_num_rows($res_referral_map) );
    if(mysql_num_rows($res_referral_map)==1){
        $row_get_referral_map=mysql_fetch_assoc($res_referral_map);
        $hospital_user_id=$row_get_referral_map['hospital_user_id'];
        $mapping_id=$row_get_referral_map['mapping_id'];
        
        $logger->write ( "INFO :", "hospital_id******** =>".$hospital_user_id.$mapping_id);

        $referral_map_pat_entry="insert into referral_mapping_patient_stub(mapping_hospital_user_id,mapping_id,patient_stub_id) values ('$hospital_user_id','$mapping_id','$patient_auto_id')";
        $referral_map_pat_entry=mysql_query($referral_map_pat_entry);
        
        $logger->write ( "INFO :", "insertion in referral_mapping_patient_stub done");

        //getting count of new patients that are not viewed by salesperson(sales view flag)
        //push for count of new patient view flag
        /* $query_push_count_pat=mysql_query("SELECT count(*) as cnt FROM `referral_mapping_patient_stub` rm where rm.mapping_hospital_user_id='$hospital_user_id' and rm.sales_view_flag=0 ",$this->db);
        $row_push_cnt_view=mysql_fetch_assoc($query_push_count_pat);
        $count_new_pat_client=$row_push_cnt_view['cnt']; */

    }else{
        //do nothing
    }
}else
{
    //do nothing for the time being
}

//end of changes for referral map entry


if ($sql_insert) {
	//for sending text message to patient
	
	$logger->write("INFO :","inside success");
	
	$msg_pat_txt=$hospital_name." :- You have been referred to ".$doc_ref_title.$doc_ref_id_name." by ".$doc_title.$doctor_name;
	//send_msg($pat_mobile,$msg_pat_txt);//commented as per requested
		
	//for sending text message to referred doctor
	$msg_doc_txt=$hospital_name. " - Patient ".$pat_name." has been referred to you by ".$doc_title.$doctor_name;
	send_msg($doc_ref_mobile_number,$msg_doc_txt);
	
	if($doc_ref_email!=''){
	    $email_sender->send_email($doc_ref_email,$msg_doc_txt);
	}
	
	
	
	//for sending text message to referring doctor
	$msg_doc_txt=$hospital_name." - Dear ".$doc_title.$doctor_name.", Thank you for referring Patient ".$pat_name;//.".You can get real time updates of this patient by downloading Referralio app on your phone";
	send_msg($doc_mob_number,$msg_doc_txt);
	
	if($doc_email!=''){
	    $email_sender->send_email($doc_email,$msg_doc_txt);
	}
	
	
	$registatoin_ids = array($gcm_id);
	$message = array("msg" => " ".$hospital_name." - ".$doc_ref_title.$doc_ref_id_name." has reffered a patient ".$pat_name,"flag_push"=> "Refer", "Refer_In_cnt" => $cnt_refer_in);
	$result = $gcm->send_notification($registatoin_ids, $message);
		
	$success = array('status' => "Success", "msg" => "Patient successfully registered", "result" => $result);
	echo json_encode($success);
	
	
}
else{
	$success = array('status' => "Failure", "msg" => "Transaction Could not be registered", "result" => $result);
	echo json_encode($success);
}

function json($data){
	if(is_array($data)){
		return json_encode($data);
	}
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
