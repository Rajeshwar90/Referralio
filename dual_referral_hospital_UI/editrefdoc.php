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

$ref_id=$_GET['refdocId'];

//query to get the  the patient information

$query_get_doc="select * from hospital_refer_in_doctor_stub where refer_in_doc_mobile='$ref_id'";
$result_get_doc=mysql_query($query_get_doc);

while($row=mysql_fetch_assoc($result_get_doc)){
    $doctor_title=$row['refer_in_doc_title'];
    $doctor_name=$row['refer_in_doc_name'];
    $doctor_email=$row['refer_in_doc_email'];
    
}

$html="<script src='javascript/doc_reg.js' type='text/javascript'></script>";
$html="<script src='javascript/textbox_restrictions.js' type='text/javascript'></script>";
$html=$html."<div class='modal-dialog' role='document'>".
        "<div class='modal-content'>".
        "<div class='modal-header'>".
        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>".
        "<h4 class='modal-title' id='myModalLabel'>".'Edit Referring doctor'."</h4>".
        "</div>".
        "<div class='modal-body'>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Doctor Name</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='Name' name='doc_name' id='doc_name'
									value='$doctor_name'>".
							"</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Doctor Email</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='Email name='doc_email' id='doc_email'
									value='$doctor_email' maxlength=2 >
							</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Doctor Mobile Number</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='10 digit mobile' name='doctor_mobile_number' id='doctor_mobile_number'
									value='$ref_id' maxlength=10 disabled>
							</div>
						</div>";
 $html=$html."<br/>";

 $html=$html."<div class='modal-footer'>".

					"<button type='button' class='btn btn-danger' onclick='Update_Doctor()'>UPDATE</button>".
				"</div>";
 
 $html=$html. "</div></div></div>";
 
 echo $html;

?>