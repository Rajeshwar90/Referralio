<?php
session_start();
if(!isset($_SESSION['Valid_Session'])){
  //redirect to login url in PHP
  header("Location: admin_main_panel.php");
  die();
}
include 'db_conn.php';
//include 'logger.php';

$hospital_name=$_POST['hospital_name'];
$hospital_location=$_POST['hospital_location'];
$hospital_username=$_POST['hospital_username'];
$hospital_password=$_POST['hospital_password'];
$hospital_image='hospital_img.png';

$visibility=1;


$hospital_in_query=mysql_query("INSERT INTO hospital_stub(hospital_image,hospital_name, hospital_location,hospital_username,hospital_password) VALUES ('$hospital_image','$hospital_name','$hospital_location','$hospital_username','$hospital_password')");

//$logger->write("INFO :","hsopital_stub query executed");

$id = mysql_insert_id();

$hos_doc_query=mysql_query("insert into doctor_stub (Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,visibility,type_value) values('','','','','','$hospital_name','','','','$hospital_location','','','$hospital_image','$id','$visibility','hospital')");

If($hospital_in_query=="Success" && $hos_doc_query=="Success"){
  //$query="select * from hospital_refer_in_doctor_stub where refer_by_hos_id=1";
  //$result=mysql_query($query);
  $success = array('status' => "Success", "msg" => "Successfully Registered");
}else{
  $success = array('status' => "Failure", "msg" => "Refer In doctor could not be registered");
}

echo json_encode($success);



?>