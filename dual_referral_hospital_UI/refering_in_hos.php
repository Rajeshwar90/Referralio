<?php
include 'db_conn.php';
//$logger->write("INFO :","before count result_query_referin".$count_queryin);
//query to check if already referredin
$result_query_referin=mysql_query($query_get_referin);
if($count_queryin>0){
  $success = array('status' => "Failed", "msg" => "Already Referred in");
}

//query to get the doctor_id if registered

if($count_query>0){
}
else{
   

}
if($doctor_id!=""){
  $doctor_stub_id=$doctor_id;
}

$refering_in_query=mysql_query("INSERT INTO hospital_refer_in_doctor_stub(refer_in_doc_title,refer_in_doc_name, refer_in_doc_mobile,refer_in_doc_email,refer_by_hos_id,doc_stub_id) VALUES ('$doctor_title','$name','$mobile','$email','$hospital_id','$doctor_stub_id')");

If($refering_in_query=="success"){
  $success = array('status' => "Success", "msg" => "Successfully Registered");
}else{
  $success = array('status' => "Failure", "msg" => "Refer In doctor could not be registered");
}
exit(0);
?>