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
$name = $_POST['name'];
$email = $_POST['email'];
$mobile=$_POST['mobile'];



$logger->write("INFO :","received post");

/* $oldPassword="";
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
 */



$update_doc="update hospital_refer_in_doctor_stub set refer_in_doc_name='$name',refer_in_doc_email='$email' where refer_in_doc_mobile='$mobile'";
$result=mysql_query($update_doc);

$update_main_doc="update doctor_stub set Doctor_name='$name',Doctor_email='$email' where Doctor_mobile_number='$mobile'";
$result_main=mysql_query($update_main_doc);

if ($result && $result_main) {
    $success = array('status' => "Success", "msg" => "Referring Doctor has been updated");
    echo json_encode($success);
}
else{
	$success = array('status' => "Failure", "msg" => "Doctor details could not be updated.Please try again later");
	echo json_encode($success);
}




?>
