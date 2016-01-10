<?php
include 'db_conn.php';
session_start ();
// print_r($_SESSION);

if (! isset ( $_SESSION ['hospital_id_SESSION'] )) {
    header ( "Location: hospital_login.php" );
    die ();
}


$hospital_id = $_SESSION ['hospital_id_SESSION'];
$hospital_name = $_SESSION ['hospital_name_SESSION'];

$patient_id=$_GET['patient'];
$hospital_id=$_GET['hospital'];
$patient_name="";
$hospital_users_array=array();


//to get the patient name
$get_pat_name="select * from patient_stub where Patient_thread_id='$patient_id'";
$res_pat_name=mysql_query($get_pat_name);
$row_pat_name=mysql_fetch_assoc($res_pat_name);
$patient_name=$row_pat_name['Patient_Name'];

//query to check any hospital user for this patient id
$hospital_user_mapping="select * from referral_mapping_patient_stub where patient_stub_id='$patient_id'";
$res_user_mapping=mysql_query($hospital_user_mapping);
$count_hospital_user_mapping=mysql_num_rows($res_user_mapping);

//query_get_hospital_users for this hospital
$query_get_hospital_users="select * from doctor_stub where type_value='hospital_user' and Doctor_yxp='$hospital_id'";
$result_get_hospital_users=mysql_query($query_get_hospital_users);
//echo mysql_num_rows($result_get_hospital_users)."hospital_id".$hospital_id;
//exit;

while($row_hospital_users=mysql_fetch_assoc($result_get_hospital_users)){
   array_push($hospital_users_array,$row_hospital_users['Doctor_serial_id']); 
}




$query_get_messages="select pat.Patient_Name,temp.* from patient_stub pat inner join (SELECT pmsg.message,pmsg.doc_id,pmsg.pat_thrd_id,ds.Doctor_name,pmsg.timestamp FROM pat_thread_msg pmsg INNER JOIN doctor_stub ds ON pmsg.login_id=ds.Doctor_serial_id where pmsg.pat_thrd_id='$patient_id' order by TImestamp ASC)as temp on temp.pat_thrd_id=pat.Patient_thread_id";

$res_get_messages=mysql_query($query_get_messages);
$count_get_messages=mysql_num_rows($res_get_messages);

while($row_get_msgs1=mysql_fetch_assoc($res_get_messages)){
    //$patient_name=$row_get_msgs1['Patient_Name'];
}



if($count_get_messages==0){
    $html="<div class='modal-dialog' role='document'>".
    "<div class='modal-content'>".
    "<div class='modal-header'>".
        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>".
        "<h4 class='modal-title' id='myModalLabel'>".$patient_name."</h4>".
    "</div>".
    "<div class='modal-body'>";
    
    $html=$html."No Transactions found for this patient". "</div>";
     if($count_hospital_user_mapping>0){
        $html=$html."<div class='send'>".
    						"<form>
    								
    							<textarea class='textarea form-control' rows='5' cols='30'
    								placeholder='Enter text ...' id='newTransactionMsg_".$patient_id."'></textarea>".
    								"<input type='hidden' name='hosname' id='hosname' value='$hospital_name'>".
    							"<button type='button' class='btn btn-danger btn-send' onClick='sendTransactionMessaging(".$patient_id.")'>".
    								"<i class='fa fa-envelope-o '></i>&nbsp;Send".
    							"</button>
    						</form>
    					</div>";
        }
		 $html=$html."</div></div>";;
}
else{
    $html="<div class='modal-dialog' role='document'>".
            "<div class='modal-content'>".
            "<div class='modal-header'>".
            "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>".
            "<h4 class='modal-title' id='myModalLabel'>".$patient_name."</h4>".
            "</div>".
            "<div class='modal-body'>";
    
    //$html=$html."<div class='post'>";
    mysql_data_seek ( $res_get_messages, 0 );
    while($row_get_msgs=mysql_fetch_assoc($res_get_messages)){
        $html=$html."<div class='post'>"."<div class='default'>";
        //condition for hospital user
        if (in_array($row_get_msgs['doc_id'], $hospital_users_array)) {
            $html=$html."<h4 class='pink'>Hospital User</h4>";
        }else{
            $html=$html."<h4 class='pink'>".$row_get_msgs['Doctor_name']."</h4>";
        }
        //end of condition for hospital user
        
        $html=$html."<p>".$row_get_msgs['message']."</p>".
        "<div class='text-right'>".
        "<span class='pink'>".
        "<i class='fa fa-calendar'></i>". $row_get_msgs['timestamp'] ."</span>".
        "<span class='pink'>".
        "<i class=' fa fa-clock-o '></i>".
        "16:22".
        "</span> </div> </div> </div>";
    }
    
    
    $html=$html."</div>";
    if($count_hospital_user_mapping>0){
    $html=$html."<div class='send'>".
						"<form>
							<textarea class='textarea form-control' rows='5' cols='30'
								placeholder='Enter text ...' id='newTransactionMsg_".$patient_id."'></textarea>".
							"<input type='hidden' name='hosname' id='hosname' value='$hospital_name'>".	
							"<button type='button' class='btn btn-danger btn-send' onClick='sendTransactionMessaging(".$patient_id.")'>".
								"<i class='fa fa-envelope-o '></i>&nbsp;Send".
							"</button>
						</form>
					</div>";
    }
    $html=$html."</div></div>";
}

echo $html;



?>