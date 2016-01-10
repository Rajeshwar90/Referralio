<?php

include 'db_conn.php';
include 'logger.php';
$logger = new Logger();
$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);

$user_name = mysql_real_escape_string($_POST['user_name']);
$user_mobile = $_POST['user_mobile'];
$user_email= mysql_real_escape_string($_POST['user_email']);
$user_password = mysql_real_escape_string($_POST['user_password']);
$hospital_id= mysql_real_escape_string($_POST['hospital_id']);
$doctor_title= mysql_real_escape_string($_POST['doctor_title']);

if($user_name == "" || $user_mobile == "" || $user_email == "" || $user_password == "" || $hospital_id == ""){
    $success = array('status' => "Failure", "msg" => "User could not be added in this hospital");
    echo json_encode($success);
    exit(0);
}

$type="hospital_user";
//$my_type="hospital_user";

$visibility=0;// not seen in universal
$imageName="account_image.png";


$query_res=mysql_query("select * from doctor_stub where Doctor_mobile_number='$user_mobile' and type_value='$type' and Doctor_yxp='$hospital_id'");
if(mysql_num_rows($query_res)>0){
    $success = array('status' => "Failure1", "msg" => "User already added in this hospital");
    echo json_encode($success);
    exit(0);
}


$query_res1=mysql_query("select * from doctor_stub where Doctor_mobile_number='$user_mobile' and type_value!='$type'");
if(mysql_num_rows($query_res1)>0){
	$success = array('status' => "Failure1", "msg" => "User is not a sales person.Might be a doctor for your organisation or some other");
	echo json_encode($success);
	exit(0);
}


$query_res2=mysql_query("select * from doctor_stub where Doctor_mobile_number='$user_mobile'");
if(mysql_num_rows($query_res2)>0){
	$success = array('status' => "Failure1", "msg" => "User is already registered with us");
	echo json_encode($success);
	exit(0);
}





$query_test="insert into doctor_stub (Doctor_Title,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,type_value,country_code) values('$doctor_title','$user_name','','$user_email','','','','','','','','$user_mobile',md5('$user_password'),'$imageName','$hospital_id','','$visibility','$type','')";


//$query_test="insert into doctor_stub (Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,type_value) values('$user_name','','$user_email','','','','','','','','$user_mobile',md5('$user_password'),'$imageName','$hospital_id','','$visibility','$type')";

$logger->write("INFO :","insert query=>".$query_test);
$res_insert=mysql_query($query_test);

if($res_insert){
    $success = array('status' => "Success", "msg" => "User added in this hospital");
    echo json_encode($success);
    exit(0);
}else{
    $success = array('status' => "Failure", "msg" => "User could not be added in this hospital");
    echo json_encode($success);
    exit(0);
}


?>