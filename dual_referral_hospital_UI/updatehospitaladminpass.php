<?php
include 'db_conn.php';
include 'logger.php';
include 'GCM.php';
$logger = new Logger();
$gcm= new GCM();
include_once 'iosPushUniversal.php';
$ios_push = new iosPushUniversal();

$logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
$logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );



$logger->write("INFO :","before post");
$hos_id = $_POST['hos_id'];
$oldpass = $_POST['oldpass'];
$newpass = $_POST['newpass'];



$logger->write("INFO :","received post");

$oldPassword="";
//query to get oldpass
$getoldpass=mysql_query("select * from hospital_stub where hospital_id='$hos_id'");
while($row_get_cred=mysql_fetch_assoc($getoldpass)){
    $oldPassword=$row_get_cred['hospital_password'];
}

if($oldPassword!=$oldpass){
    $success = array('status' => "Failure", "msg" => "Old Password does not match.Try again");
    echo json_encode($success);
    exit(0);
}




$update_pass="update hospital_stub set hospital_password='$newpass' where hospital_id='$hos_id'";
$result=mysql_query($update_pass);


if ($result) {
    $success = array('status' => "Success", "msg" => "Password has been updated");
    echo json_encode($success);
}
else{
	$success = array('status' => "Failure", "msg" => "Password could not be updated.Please try again later");
	echo json_encode($success);
}




?>
