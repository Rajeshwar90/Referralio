<?php/*note:When a doctor get referred it needs to get inserted in doctor stub table with a doctorSerialID and 'temp' in Doctor_unregistered tablewhen doctor registering before checking hospital doctor, check whther this mobile number present in hospital_refer_in_doctor_stubif present update entry then update doc id hospital_refer_in_doc_stub where mobile number and also update doctor_unregistered as fals in doctor stub table, and then go for hospital_refer_out_doc_stubend of note */session_start();if(!isset($_SESSION['hospital_id_SESSION'])){  header("Location: hospital_login.php");  die();}
include 'db_conn.php';include 'Email_sender.php';//include 'logger.php';//$logger=new Logger();$email_sender=new EMAIL_SENDER();$hospital_name="";$hospital_id=$_POST['hospital_id'];$mobile=$_POST['mobile'];$name=$_POST['name'];$email=$_POST['email'];$doctor_title=$_POST['doctor_title'];$link="https://play.google.com/store/apps/details?id=com.hospitalcheck.referralio&hl=en";
//$logger->write("INFO :","before count result_query_referin".$count_queryin);
//query to check if already referredin$query_get_referin="select * from hospital_refer_in_doctor_stub where refer_by_hos_id='$hospital_id' and refer_in_doc_mobile='$mobile'";
$result_query_referin=mysql_query($query_get_referin);$count_queryin=mysql_num_rows($result_query_referin);
if($count_queryin>0){  //$logger->write("INFO :","count result_query_referin".$count_queryin);
  $success = array('status' => "Failed", "msg" => "Already Referred in");  echo json_encode($success);  exit(0);
}//$logger->write("INFO :","after count result_query_referin".$count_queryin);
//query to get the hospital name$query_get_hospital_name="select * from hospital_stub where hospital_id='$hospital_id'";$result_gethos=mysql_query($query_get_hospital_name);while($row_get_hospital_name=mysql_fetch_assoc($result_gethos)){	$hospital_name=$row_get_hospital_name['hospital_name'];	//$doctor_name=$row_get_hospital_name['Doctor_'];}$Doctor_HospitalName=$hospital_name;
//query to get the doctor_id if registered$query_get="select * from doctor_stub where Doctor_mobile_number='$mobile'";$result_query=mysql_query($query_get);$count_query=mysql_num_rows($result_query);$doctor_stub_id="";

if($count_query>0){    $temp='temp';     $query_check_temp="select * from doctor_stub where Doctor_mobile_number='$mobile' and Doctor_unregistered='$temp'";    $res_check_temp=mysql_query($query_check_temp);    $count_chk_tmp=mysql_num_rows($res_check_temp);    if($count_chk_tmp>0){            }else{                while($row=mysql_fetch_assoc($result_query)){            $doctor_id=$row['Doctor_serial_id'];        }            }     	  	  //It was asked not to make the visibility update when doctor gets referred	  //$visibility=0;	  //$query_visibility_update=mysql_query("update doctor_stub set visibility='$visibility' where Doctor_serial_id=".$doctor_id);
}
else{
       //query for inserting referring_in in doctor_stub    $temp="temp";    $visibility=1;    $insert_pending_referring_in=mysql_query("insert into doctor_stub (Doctor_Title,Doctor_name,Doctor_mobile_number,visibility,Doctor_unregistered) values('$doctor_title','$name','$mobile','$visibility','$temp')");

}
if($doctor_id!=""){
  $doctor_stub_id=$doctor_id;
}

$refering_in_query=mysql_query("INSERT INTO hospital_refer_in_doctor_stub(refer_in_doc_title,refer_in_doc_name, refer_in_doc_mobile,refer_in_doc_email,refer_by_hos_id,doc_stub_id) VALUES ('$doctor_title','$name','$mobile','$email','$hospital_id','$doctor_stub_id')");//query to get the helper clarification table$getClarificationHelper=mysql_query("select * from clarification_member_by_hospital where hospital_id='$hospital_id'");$row_helper=mysql_fetch_assoc($getClarificationHelper);$person=$row_helper['person_name'];$mobile_number=$row_helper['mobile_number'];$extra_notes=$row_helper['extra_notes'];$clarification_msg="For any help, contact ".$person." at ".$mobile_number.".Thanks.".$Doctor_HospitalName.".".$extra_notes;$mobile_txt_msg=$Doctor_HospitalName.": Dear ".$doctor_title.$name." You have been added as a Referring doctor by ".$Doctor_HospitalName.". Please use this below link to download the app: ".$link."  Or you can search for Referralio on android or ios app store";send_msg($mobile,$mobile_txt_msg);if($email!=''){    $email_sender->send_email($email,$mobile_txt_msg);}

If($refering_in_query=="success"){      send_msg($mobile,$clarification_msg);
  $success = array('status' => "Success", "msg" => "Successfully Registered");
}else{
  $success = array('status' => "Failure", "msg" => "Refer In doctor could not be registered");
}echo json_encode($success);
exit(0);     function send_msg($mobile,$mobile_msg){		    			//$logger->write("INFO :","login with mobile".$mobile);		    			$msg="";									$msg=urlencode($mobile_msg);			  									//$logger->write("INFO :","login with msg".$msg);			$url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;						//$logger->write("INFO :","login with url".$url);            			$ch = curl_init();  // setup a curl			curl_setopt($ch, CURLOPT_URL, $url);  // set url to send to			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 			$output=curl_exec($ch);           //echo "output".$output;			curl_close($ch);			//return $output;		} 
?>