<?php
session_start();
include 'db_conn.php';

//$hospital_id=$_SESSION['hospital_id'];
$hospital_id=$_REQUEST['hospital_id'];
$mobile=$_REQUEST['mobile'];
$name=$_REQUEST['name'];
$email=$_REQUEST['email'];

//query to check if already referredin
$query_get_referin="select * from hospital_refer_in_doctor_stub where refer_by_hos_id='$hospital_id' and refer_in_doc_mobile='$mobile'";
$result_query_referin=mysql_query($query_get_referin);
$count_queryin=mysql_num_rows($result_query_referin);

if($count_queryin>0){
  $success = array('status' => "Failed", "msg" => "Already Reffered in");
  echo json_encode($success);
  exit(0);
}


//query to get the doctor_id if registered
$query_get="select * from doctor_stub where Doctor_email='$email' and Doctor_mobile_number='$mobile'";
$result_query=mysql_query($query_get);
$count_query=mysql_num_rows($result_query);
$doctor_stub_id="";

if($count_query>0){
    while($row=mysql_fetch_assoc($result_query)){
	   $doctor_id=$row['Doctor_serial_id'];
	}		$visibility=0;	$query_visibility_update=mysql_query("update doctor_stub set visibility='$visibility' where Doctor_serial_id=".$doctor_id);
}
else{

//$success = array('status' => "Successful", "msg" => "Message sent to doctor for installing");
//sent the doctor a message to install

}
if($doctor_id!=""){
  $doctor_stub_id=$doctor_id;
}

$refering_in_query=mysql_query("INSERT INTO hospital_refer_in_doctor_stub(refer_in_doc_name, refer_in_doc_mobile,refer_in_doc_email,refer_by_hos_id,doc_stub_id) VALUES ('$name','$mobile','$email','$hospital_id','$doctor_stub_id')");	

If($refering_in_query=="success"){
  //$query="select * from hospital_refer_in_doctor_stub where refer_by_hos_id=1";
  //$result=mysql_query($query);
  $success = array('status' => "Success", "msg" => "Successfully Registered");
}else{
  $success = array('status' => "Failure", "msg" => "Refer In doctor could not be registered");
}

echo json_encode($success);
exit(0);



?>