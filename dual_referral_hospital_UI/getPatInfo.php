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
$html=$html."<div class='modal-dialog' role='document'>".
        "<div class='modal-content'>".
        "<div class='modal-header'>".
        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>".
        "<h4 class='modal-title' id='myModalLabel'>".'Edit Patient'."</h4>".
        "</div>".
        "<div class='modal-body'>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Patient Name</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='patient age' name='patient_name' id='patient_name'
									value='$patient_name'>".
							"</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Patient Age</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='patient age' name='patient_age' id='patient_age'
									value='$patient_age' maxlength=2 >
							</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Mobile Number</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='10 digit mobile' name='patient_mobile' id='mobile'
									value='$patient_mobile' maxlength=10>
							</div>
						</div>";
 $html=$html."<br/>";
 $html=$html."<input type='hidden' name='patient_id' id='patient_id'
 value='$patient_id' />";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Gender</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='10 digit mobile' name='patient_gender' id='patient_gender'
									value='$patient_gender'>
							</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
							"<label class='col-lg-4 control-label'>Location</label>".
							"<div class='col-lg-8'>".
								"<input type='text' class='form-control'".
									"placeholder='Patient Location' name='patient_loc' id='patient_loc'
									value='$patient_loc'>
							</div>
						</div>";
 $html=$html."<br/>";
 $html= $html. "<div class='form-group'>".
         "<label class='col-lg-4 control-label'>Notes</label>".
         "<div class='col-lg-8'>".
         "<textarea name='patient_notes' id='patient_notes' class='form-control'".
         "placeholder='Patient Notes'
         value='$patient_notes'>$patient_notes</textarea>
         </div>
         </div>";
 
 
 $html=$html."<br/>";
 
 $html=$html."<div class='modal-footer'>".

					"<button type='button' class='btn btn-danger' onclick='UpdatePatient()'>UPDATE</button>".
				"</div>";
 
 $html=$html. "</div></div></div>";
 
 echo $html;

?>