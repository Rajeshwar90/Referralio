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

//query to get the  the patient information

$query_get_pat="select * from patient_stub where Patient_thread_id='$patient_id'";
$result_get_pat=mysql_query($query_get_pat);

while($row=mysql_fetch_assoc($result_get_pat)){
    $patient_name=$row['Patient_Name'];
    $patient_age=$row['Patient_Age'];
    $patient_mobile=$row['Patient_mobile_number'];
    $patient_gender=$row['Patient_Gender'];
    $patient_loc=$row['Patient_Location'];
    $patient_notes=$row['Patient_issue_notes'];
    
}

$html="<script src='javascript/doc_reg.js' type='text/javascript'></script>";
$html="<script src='javascript/textbox_restrictions.js' type='text/javascript'></script>";
$html="<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.js'></script>";
$html="<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>";

$html="<script type='text/javascript'>
$(document).ready(function() {
        $('#submitbtn').click(function() {
          alert('hello123');
            $('.uploadform').ajaxForm({
                target: '#file'
            }).submit();
        });
});</script>";

$html=$html."<form method='post' action='fileuploadpost.php' class='uploadform' enctype='multipart/form-data'>";
$html=$html."<div class='modal-dialog' role='document'>".
        "<div class='modal-content'>".
        "<div class='modal-header'>".
        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>".
        "<h4 class='modal-title' id='myModalLabel'>".'Upload discharge document'."</h4>".
        "</div>".
        "<div class='modal-body'>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Patient Name</label>".
							"<div class='col-lg-8'>".
								$patient_name."(".$patient_mobile.")".
							"</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Discharge Document</label>".
							"<div class='col-lg-8'>".
								"<input type='file' class='form-control'".
									"placeholder='Upload' name='upload_file'>
							</div>
						</div>";
 $html=$html."<br/>";
 
 $html=$html."<div id='file' name='file' >"."</div>";
 
 $html=$html."<br/>";
 
 $html=$html."<div class='modal-footer'>".

					"<input type='submit' name='submitbtn' id= 'submitbtn' class='btn btn-danger' value='Submit'/>".
				"</div>";
 
 
 
 $html=$html. "</div></div></div></form>";
 
 echo $html;

?>