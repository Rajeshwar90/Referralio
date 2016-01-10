<?php
include 'db_conn.php';
/*session_start ();
// print_r($_SESSION);

if (! isset ( $_SESSION ['hospital_id_SESSION'] )) {
    header ( "Location: hospital_login.php" );
    die ();
}


$hospital_id = $_SESSION ['hospital_id_SESSION'];
$hospital_name = $_SESSION ['hospital_name_SESSION'];*/

$name=$_POST['name'];
$mob_number=$_POST['mob_number'];
$pass=$_POST['pass'];
$pass=md5($pass);
//$action=$_POST['action'];
$hospital_id=$_POST['hospital_id'];
$pic='account_image.png';
$visibility=0;
$type='hospital_user_all';

/* if($action==0){//new insert
    $query_insert_help="insert into clarification_member_by_hospital(hospital_id,person_name,mobile_number,extra_notes) values('$hospital_id','$name','$mob_number','$extra_notes')";
    $result_query_insert_help=mysql_query($query_insert_help);
}else{//update
    $query_insert_help="update clarification_member_by_hospital set person_name = '$name',mobile_number='$mob_number',extra_notes='$extra_notes' where hospital_id='$hospital_id'";
    $result_query_insert_help=mysql_query($query_insert_help);
}
 */

$query_insertmgmt="insert into doctor_stub(Doctor_name,Doctor_mobile_number,Doctor_password,Doctor_photograph,Doctor_yxp,visibility,type_value) values ('$name','$mob_number','$pass','$pic','$hospital_id',$visibility,'$type')";

$result_query_insert_help=mysql_query($query_insertmgmt);

if ($result_query_insert_help) {

    $success = array (
            'status' => "Success",
            "msg" => "Management user added successfully"
    );
    echo json_encode ( $success );
} else {
    $success = array (
            'status' => "Failure",
            "msg" => $query_insertmgmt
    );
    echo json_encode ( $success );
}

?>