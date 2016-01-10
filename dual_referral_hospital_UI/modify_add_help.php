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
$extra_notes=$_POST['extra_notes'];
$action=$_POST['action'];
$hospital_id=$_POST['hospital_id'];

if($action==0){//new insert
    $query_insert_help="insert into clarification_member_by_hospital(hospital_id,person_name,mobile_number,extra_notes) values('$hospital_id','$name','$mob_number','$extra_notes')";
    $result_query_insert_help=mysql_query($query_insert_help);
}else{//update
    $query_insert_help="update clarification_member_by_hospital set person_name = '$name',mobile_number='$mob_number',extra_notes='$extra_notes' where hospital_id='$hospital_id'";
    $result_query_insert_help=mysql_query($query_insert_help);
}

if ($result_query_insert_help) {

    $success = array (
            'status' => "Success",
            "msg" => "Helping user added successfully"
    );
    echo json_encode ( $success );
} else {
    $success = array (
            'status' => "Failure",
            "msg" => "Helping user could not be added.Please try after sometime"
    );
    echo json_encode ( $success );
}

?>