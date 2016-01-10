<?php
include 'db_conn.php';
include 'logger.php';
$logger = new Logger();

$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);

include_once 'GCM.php';
include_once 'iosPushUniversal.php';
$gcm = new GCM();
$ios_push = new iosPushUniversal();

$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);


$patient_id=$_POST['patientid'];
$message=$_POST['message'];

$hospital_user_id="";

//query to check any hospital user for this patient id
$hospital_user_mapping="select * from referral_mapping_patient_stub where patient_stub_id='$patient_id'";
$res_user_mapping=mysql_query($hospital_user_mapping);
$count_hospital_user_mapping=mysql_num_rows($res_user_mapping);
while ($row_get_user_mapping=mysql_fetch_assoc($res_user_mapping)){
    $hospital_user_id=$row_get_user_mapping['mapping_hospital_user_id'];
}

//to get the original reg_by_doc and doc_ref_id for push
$reg_by_doc='';
$doc_ref_id='';
$unread_flag='unread';
$query_get_doc_id="select * from patient_stub where Patient_thread_id='$patient_id'";
$res_get_doc_id=mysql_query($query_get_doc_id);
while($row_get_doc_id=mysql_fetch_assoc($res_get_doc_id))
{
   $reg_by_doc=$row_get_doc_id['Reg_by_doc'];
   $doc_ref_id=$row_get_doc_id['doc_ref_id'];
}

$registration_ids_reg_by_doc='';
$mobile_os_reg="";
$mobile_os_ref="";
$registration_ids_doc_ref_id='';
$gcm_mysql_res_reg=mysql_query("select tmp.*, gc.gcm_regid, gc.mobile_os_type from gcm_users gc inner join (select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$reg_by_doc')as tmp on tmp.Doctor_mobile_number=gc.mob_number");

while($row_get_reg_id=mysql_fetch_assoc($gcm_mysql_res_reg)){
    //array_push($registration_ids_reg_by_doc,$row_get_reg_id['gcm_regid']);
    $registration_ids_reg_by_doc=$row_get_reg_id['gcm_regid'];
    $mobile_os_reg=$row_get_reg_id['mobile_os_type'];
}


$gcm_mysql_res_ref=mysql_query("select tmp.*, gc.gcm_regid, gc.mobile_os_type from gcm_users gc inner join (select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$doc_ref_id')as tmp on tmp.Doctor_mobile_number=gc.mob_number");

while($row_get_ref_id=mysql_fetch_assoc($gcm_mysql_res_ref)){
    //array_push($registration_ids_doc_ref_id,$row_get_ref_id['gcm_regid']);
    $registration_ids_doc_ref_id=$row_get_ref_id['gcm_regid'];
    $mobile_os_ref=$row_get_ref_id['mobile_os_type'];
}

$logger->write("INFO :","data for gcm".$mobile_os_reg."Ref***==>".$mobile_os_ref);

$login_id=$reg_by_doc;
$pat_thrd_id=$patient_id;

//inserting message
$query_insert_msg=mysql_query("INSERT INTO pat_thread_msg(pat_thrd_id, login_id,doc_id,message,read_flag) VALUES ('$patient_id','$hospital_user_id','$hospital_user_id','$message','$unread_flag')");

$logger->write("INFO :","****message inserted****");

if($query_insert_msg){
    
    //getting message count details
    $count_msg=mysql_query("select count(*) as cnt from pat_thread_msg pmsg where pat_thrd_id='$patient_id' and pmsg.read_flag='unread'");
    $result_cnt = mysql_fetch_array($count_msg,MYSQL_ASSOC);
    $cnt=$result_cnt['cnt'];
    
    //getting patient details
    $get_pat_sql=mysql_query("select * from patient_stub where Patient_thread_id='$pat_thrd_id'");
    $result_get_pat_name = mysql_fetch_array($get_pat_sql,MYSQL_ASSOC);
    $pat_name_fetch=$result_get_pat_name['Patient_Name'];
    $pat_refer_date=$result_get_pat_name['TImestamp'];
    $pat_gender=$result_get_pat_name['Patient_Gender'];
    $pat_age=$result_get_pat_name['Patient_Age'];
    $pat_loc=$result_get_pat_name['Patient_Location'];
    $pat_mobile=$result_get_pat_name['Patient_mobile_number'];
    $pat_note=$result_get_pat_name['Patient_defined_notes'];
    $pat_issue_note=$result_get_pat_name['Patient_issue_notes'];
    
    //get doctor name
    $get_doc_name=mysql_query("select Doctor_name from doctor_stub where Doctor_serial_id='$login_id'");
    $result_get_doc_name = mysql_fetch_array($get_doc_name,MYSQL_ASSOC);
    $doc_name_fetch=$result_get_doc_name['Doctor_name'];
    
    $hospital_user_name="Hospital User";
    
    /*if($mobile_os_ref=='Android'){
        
        $logger->write("INFO :","inside push Android mobile number".$registration_ids_doc_ref_id);
        //sending push message to all refer out doctors Android
        $registatoin_ids = array($registration_ids_doc_ref_id);
        $req_val=array("patient_name" => $pat_name_fetch,"REFER_DATE" => $pat_refer_date,"doctor_name" => $hospital_user_name,"doctor_id"=> $hospital_user_id,"pat_thread_id" => $pat_thrd_id,"pat_gender"=>$pat_gender,"pat_age"=>$pat_age,"pat_loc"=>$pat_loc,"pat_mobile"=>$pat_mobile,"pat_note"=>$pat_note,"pat_issue_note"=>$pat_issue_note,"cnt"=>$cnt);
        $msg=json($req_val);
        $message = array("msg" => $msg,"flag_push"=>"Message");
        $logger->write("INFO :","inside push Android mobile number before gcm");
        $result = $gcm->send_notification($registatoin_ids, $message);
        $logger->write("INFO :","inside push Android mobile number after gcm");
        $logger->write("INFO :","result".$result);
        
        $success = array('status' => "Success", "msg" => "Successfully msg has been sent");
        echo json_encode($success);
        exit(0);
        
    }else if($mobile_os_ref=='IOS'){

        $logger->write("INFO :","inside IOS".$registration_ids_doc_ref_id);
        $registatoin_ids = array($registration_ids_doc_ref_id);
        $message ="Message from Hospital user"."for patient ".$pat_name_fetch;
        $req_val=array("patient_name" => $pat_name_fetch,"REFER_DATE" => $pat_refer_date,"doctor_name" => $hospital_user_name,"doctor_id"=> $hospital_user_id,"pat_thread_id" => $pat_thrd_id,"pat_gender"=>$pat_gender,"pat_age"=>$pat_age,"pat_loc"=>$pat_loc,"pat_mobile"=>$pat_mobile,"pat_note"=>$pat_note,"pat_issue_note"=>$pat_issue_note,"cnt"=>$cnt);
        $msg=json($req_val);
        $extra = array("msg" => $msg,"flag_push"=>"Message");
        $logger->write("INFO :","inside push Android mobile number before ios gcm");
        $result_ios = $ios_push->sendIosPush($registatoin_ids,$message,$extra);
        $logger->write("INFO :","inside push Android mobile number before ios gcm");
        $logger->write("INFO :","inside IOS result".$result_ios);
        
        
        $success = array('status' => "Success", "msg" => "Successfully msg has been sent");
        echo json_encode($success);
        exit(0);
        	
    }*/
    
    if($mobile_os_reg=='Android'){
    
    $logger->write("INFO :","inside push Android mobile number".$registration_ids_reg_by_doc);
    //sending push message to all refer out doctors Android
    $registatoin_ids = array($registration_ids_reg_by_doc);
    $req_val=array("patient_name" => $pat_name_fetch,"REFER_DATE" => $pat_refer_date,"doctor_name" => $hospital_user_name,"doctor_id"=> $hospital_user_id,"pat_thread_id" => $pat_thrd_id,"pat_gender"=>$pat_gender,"pat_age"=>$pat_age,"pat_loc"=>$pat_loc,"pat_mobile"=>$pat_mobile,"pat_note"=>$pat_note,"pat_issue_note"=>$pat_issue_note,"cnt"=>$cnt);
    $msg=json($req_val);
    $message = array("msg" => $msg,"flag_push"=>"Message");
    $logger->write("INFO :","inside push Android mobile number before gcm");
    $result = $gcm->send_notification($registatoin_ids, $message);
    $logger->write("INFO :","inside push Android mobile number after gcm");
    $logger->write("INFO :","result".$result);
    
    $success = array('status' => "Success", "msg" => "Successfully msg has been sent");
    echo json_encode($success);
    exit(0);
    
    }else if($mobile_os_ref=='IOS'){
    
    $logger->write("INFO :","inside IOS".$registration_ids_reg_by_doc);
    $registatoin_ids = array($registration_ids_reg_by_doc);
    $message ="Message from Hospital user"."for patient ".$pat_name_fetch;
    $req_val=array("patient_name" => $pat_name_fetch,"REFER_DATE" => $pat_refer_date,"doctor_name" => $hospital_user_name,"doctor_id"=> $hospital_user_id,"pat_thread_id" => $pat_thrd_id,"pat_gender"=>$pat_gender,"pat_age"=>$pat_age,"pat_loc"=>$pat_loc,"pat_mobile"=>$pat_mobile,"pat_note"=>$pat_note,"pat_issue_note"=>$pat_issue_note,"cnt"=>$cnt);
    $msg=json($req_val);
    $extra = array("msg" => $msg,"flag_push"=>"Message");
    $logger->write("INFO :","inside push Android mobile number before ios gcm");
    $result_ios = $ios_push->sendIosPush($registatoin_ids,$message,$extra);
    $logger->write("INFO :","inside push Android mobile number before ios gcm");
    $logger->write("INFO :","inside IOS result".$result_ios);
    
    
    $success = array('status' => "Success", "msg" => "Successfully msg has been sent");
    echo json_encode($success);
    exit(0);
     
    }
    
}else{
    $success = array('status' => "Failure", "msg" => "Message could not be sent.Please try again later");
    echo json_encode($success);
    exit(0);
}


function json($data){

    if(is_array($data)){

        return json_encode($data);

    }

}

?>