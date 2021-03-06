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
// $hospital_name="ABC Hospital";
// $hospital_id=1;

$transactions_query = "select res2.*, Doctor_name as doc_ref_id_Doctor_name,Doctor_mobile_number as doc_ref_id_Doctor_mobile_number from (select res1.*,Doctor_name as reg_by_doc_Doctor_name,Doctor_mobile_number as reg_by_doc_Doctor_mobile_number from (SELECT Patient_thread_id,Patient_Name,Patient_mobile_number,Reg_by_doc,doc_ref_id,primary_status,secondary_status,created_by,Timestamp FROM patient_stub ps inner join hospital_refer_out_doctor_stub hds on ps.Reg_by_doc=hds.doctor_stub_id or ps.doc_ref_id=hds.doctor_stub_id where hds.hospital_id='$hospital_id' order by Timestamp desc)as res1 inner join doctor_stub ds on res1.Reg_by_doc=ds.Doctor_serial_id)as res2 inner join doctor_stub sd on res2.doc_ref_id=sd.Doctor_serial_id";

$result_transactions = mysql_query ( $transactions_query );
$count_result_transactions = mysql_num_rows ( $result_transactions );

$refer_out_query = "select * from hospital_refer_out_doctor_stub hs inner join doctor_stub ds on hs.doctor_stub_id=ds.Doctor_serial_id and hs.hospital_id='$hospital_id'";
$result_refer_out_query = mysql_query ( $refer_out_query );
$count_refer_out_query = mysql_num_rows ( $result_refer_out_query );

$referring_in_query = "select * from hospital_refer_in_doctor_stub hs inner join doctor_stub ds on hs.doc_stub_id=ds.Doctor_serial_id and hs.refer_by_hos_id='$hospital_id'";
$result_referring_in_query = mysql_query ( $referring_in_query );
$count_refer_in_query = mysql_num_rows ( $result_referring_in_query );

//$query_referring_in_analytics = "select  Doctor_name,Reg_by_doc,count(*) as cnt,doc_ref_id from patient_stub ps inner join doctor_stub ds on ps.Reg_by_doc=ds.Doctor_serial_id where Reg_by_doc in (select doc_stub_id from hospital_refer_in_doctor_stub rs where rs.refer_by_hos_id='$hospital_id' and rs.doc_stub_id!= '') and doc_ref_id in(select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id='$hospital_id' )group by Reg_by_doc";
//$result_referring_in_analytics = mysql_query ( $query_referring_in_analytics );

//$query_hospital_doc_analytics = "Select Doctor_name,Reg_by_doc,count(*)as cnt from patient_stub ps inner join doctor_stub ds on ps.Reg_by_doc=ds.Doctor_serial_id where Reg_by_doc in(select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id='$hospital_id')";
//$result_hospital_doc_analytics = mysql_query ( $query_hospital_doc_analytics );

$pending_referring_doctors="select * from hospital_refer_in_doctor_stub where refer_by_hos_id='$hospital_id' and doc_stub_id=''";
$result_pending_referring_doctors=mysql_query($pending_referring_doctors);
$count_pending_refer_in=mysql_num_rows($result_pending_referring_doctors);

$query_get_refer_in_msgs="select message_title,message_content,datetime,Doctor_name from doc_broadcast_msg db  inner join doctor_stub ds on db.doctor_id=ds.Doctor_serial_id where doctor_id IN (SELECT doc_stub_id FROM `hospital_refer_in_doctor_stub` where refer_by_hos_id='$hospital_id') and hospital_id_author='$hospital_id' ORDER BY datetime DESC ";
$res_query_get_refer_in_msgs=mysql_query($query_get_refer_in_msgs);

$hospital_users="select * from doctor_stub where Doctor_yxp='$hospital_id' and type_value='hospital_user'";
$result_hospital_users=mysql_query($hospital_users);
$count_hospital_users=mysql_num_rows($result_hospital_users);


$query_get_refer_out_msgs="select message_title,message_content,Doctor_name,datetime from doc_broadcast_msg db inner join doctor_stub ds on db.doctor_id=ds.Doctor_serial_id where doctor_id IN (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id='$hospital_id') and hospital_id_author = '$hospital_id' order by datetime desc";
$res_query_get_refer_out_msgs=mysql_query($query_get_refer_out_msgs);

// select res1.*,Doctor_name as message_to_doc from (SELECT pat_thrd_id,login_id,doc_id,message,timestamp,Doctor_name as message_from_doc FROM `pat_thread_msg` pmsg inner join doctor_stub ds on pmsg.login_id=ds.Doctor_serial_id where pmsg.pat_thrd_id=2 order by timestamp desc) as res1 inner join doctor_stub ds1 where res1.doc_id=ds1.Doctor_serial_id

$query_referring_in_analytics="select  Doctor_name,Reg_by_doc,count(*) as cnt,doc_ref_id from patient_stub ps inner join doctor_stub ds on ps.Reg_by_doc=ds.Doctor_serial_id where Reg_by_doc in (select doc_stub_id from hospital_refer_in_doctor_stub rs where rs.refer_by_hos_id='$hospital_id' and rs.doc_stub_id!= '') and doc_ref_id in(select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id='$hospital_id' )group by Reg_by_doc";
$result_referring_in_analytics=mysql_query($query_referring_in_analytics);

$row_graph1="";
$doctor_name_array1=array();
$cnt_refer_in_array1=array();
while($row_graph1=mysql_fetch_assoc($result_referring_in_analytics))
{
	$values1[] = array('label' =>$row_graph1['Doctor_name'], 'value' => $row_graph1['cnt']);
	array_push($doctor_name_array1,$row_graph1['Doctor_name']);
	array_push($cnt_refer_in_array1,$row_graph1['cnt']);
}

$hospital_doc_to_encode = $values1;


$query_hospital_doc_analytics="Select Doctor_name,doc_ref_id,count(*) as cnt from patient_stub ps inner join doctor_stub ds on ps.doc_ref_id=ds.Doctor_serial_id where doc_ref_id in(select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id='$hospital_id')  group by doc_ref_id order by cnt desc";

$result_hospital_doc_analytics=mysql_query($query_hospital_doc_analytics);
$row_graph="";
$doctor_name_array=array();
$cnt_referred_array=array();
while($row_graph=mysql_fetch_assoc($result_hospital_doc_analytics))
{
	$values[] = array('label' =>$row_graph['Doctor_name'], 'value' => $row_graph['cnt']);
	array_push($doctor_name_array,$row_graph['Doctor_name']);
	array_push($cnt_referred_array,$row_graph['cnt']);
}

$to_encode = $values;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="">
<meta name="keywords" content="hospital, checkin, doctors, corporate">
<title></title>

<!-- Mobile Specific Metas================================================== -->
<meta name="viewport"
	content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- Bootstrap  -->
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
<!-- Custom css -->
<link type="text/css" rel="stylesheet" href="css/style.css">
<!-- Favicons================================================== -->
<link rel="shortcut icon" href="images/favicon.ico">
<!-- Font awesome icons================================================== -->
<link href="css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/bootstrap-select.css">
<script src="javascript/doc_reg.js" type="text/javascript"></script>
<script src="javascript/textbox_restrictions.js" type="text/javascript"></script>

<!-- Added for graph -->
<script type="text/javascript" src="fusion/js/fusioncharts.js"></script>
<script type="text/javascript" src="fusion/js/themes/fusioncharts.theme.fint.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
  FusionCharts.ready(function(){
	//var str="[{"label":"Sudhir Ranjan","value":"3"},{"label":"Francis Meher","value":"6"}]";
	var myArray = <?php echo json_encode($hospital_doc_to_encode); ?>;
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        "width": "500",
        "height": "300",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "Reffering Doctor's Analysis",
            "subCaption": "Referring Count VS Doctor",
            "xAxisName": "Doctor",
            "yAxisName": "Referring Count",
            "theme": "fint"
         },
         //"data":[{"label":"Sudhir Ranjan","value":"3" },{"label":"Francis Meher","value":"6"}]
         "data": myArray
      }

  });

   var isArray = <?php echo json_encode($to_encode); ?>;
   var hospital_doc_chart=new FusionCharts({
       "type": "column2d",
       "renderAt": "chartContainer1",
       "width": "500",
       "height": "300",
       "dataFormat": "json",
       "dataSource":  {
         "chart": {
           "caption": "Hospital Doctor's Analysis",
           "subCaption": "Referred Count VS Doctor",
           "xAxisName": "Hospital Doctor",
           "yAxisName": "Referred Count",
           "theme": "fint"
        },
        //"data":[{"label":"Sudhir Ranjan","value":"3" },{"label":"Francis Meher","value":"6"}]
        "data": isArray
     }

 });
revenueChart.render();
hospital_doc_chart.render();
})
window.scrollTo(0, 0); 
</script>
	<!-- End of addition for graph -->
</head>
<body>

<!-- Modal for adding messages to transacctions -->
<div class="modal fade" id="transaction-patient-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<!-- end of modal -->

	<!-- Modal add hospital doctor-->
	<div class="modal fade" id="addhspdoctor" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Hospital Doctor</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">

						<div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="10 digit mobile" name="mobile" id="mobile"
									value="" maxlength=10
									onKeyPress="return restrictInput(this,event,digitsOnly)">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Doctor Name</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder=""
									name="name" id="name" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Hospital</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="hospital_name"
									id="hospital_name" value="<?php echo $hospital_name;?>"
									disabled>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Age</label>
							<div class="col-lg-8">
								<input type="text" name="dob" id="dob" class="form-control"
									placeholder="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Email ID</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="abc@test.com" name="email" id="email"
									onblur="validateEmail(this);">
							</div>
						</div>

						<!-- <div class="form-group">
							<label class="col-lg-4 control-label">Specialization</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="Specialization"
									id="Specialization" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>-->
						<input type="hidden" name="hospital_id" id="hospital_id"
							value="<?php echo $hospital_id ?>" />
						<div class="form-group">
							<label class="col-lg-4 control-label">Qualification</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="qualification"
									id="qualification" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Specialization</label>
							<div class="col-lg-8">
									<select name="specialization" id="specialization" class="form-control">
									<option value="Select">Select</option>
									
									<?php
									
									$getSpec=mysql_query("select * from Specialization order by spec_name ASC");
									while($row_spec=mysql_fetch_assoc($getSpec)){
									?>
									<option name="<?php echo $row_spec['spec_name']?>"><?php echo $row_spec['spec_name']?></option>
									<?php
									}
									?>
									</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">If Other Specialization</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="other_spec"
									id="other_spec" value=""/>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Country</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="country"
									id="country" value="" onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">State</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="state" id="state"
									value="" onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">City</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="city" id="city"
									value="" onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Address</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="address"
									id="address" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Years Experienced</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="yexp" id="yexp"
									value="" maxlength=2
									onKeyPress="return restrictInput(this,event,digitsOnly);">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">License Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="license_number" id="license_number" value="">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Type</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="my_type" id="my_type" value="I am a doctor" disabled>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Country Code</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="country_code" id="country_code" value="+91" disabled>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control" name="pass1"
									id="pass1" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Confirm Password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control" name="pass2"
									id="pass2" value="">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger" onClick="Register()">ADD</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal add Referring doctor-->
	
	<div class="modal fade" id="addrepdoctor" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Referring Doctor</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">

						<div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="10 digit mobile" name="refer_mobile"
									id="refer_mobile" value="" maxlength=10
									onKeyPress="return restrictInput(this,event,digitsOnly)">
							</div>
						</div>
						<input type="hidden" name="hospital_id" id="hospital_id"
							value="<?php echo $hospital_id ?>" />

						<div class="form-group">
							<label class="col-lg-4 control-label">Doctor Name</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder=""
									name="refer_name" id="refer_name" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>


						<!-- onblur="validateEmail(this);" -->
						<div class="form-group">
							<label class="col-lg-4 control-label">Email ID</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="abc@test.com" name="refer_email" id="refer_email"
									value="">
							</div>
						</div>

					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger" onClick="Refer()">ADD</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Add transction doctor modal -->
	<div class="modal fade" id="addTransactions" tabindex="-1"
		role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Transactions Doctor</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<label class="col-lg-4 control-label">Patient Name</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder=""
									name="tran_name" id="tran_name" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="10 digit mobile #" name="tran_mobile"
									id="tran_mobile" value="" maxlength=10
									onKeyPress="return restrictInput(this,event,digitsOnly)">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Age</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="tran_age"
									id="tran_age" value="" maxlength=3
									onKeyPress="return restrictInput(this,event,digitsOnly)">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Gender</label>
							<div class="col-lg-8">
								<div class="input-group">
									<select id="tran_gender" class="selectpicker  form-control">
										<option value="Select" selected>Please Select</option>
										<option value="Male">Male</option>
										<option value="Female">Female</option>

									</select>
								</div>

							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Location</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="tran_location"
									id="tran_location" value="">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Referring Doctor Name</label>
							<div class="col-lg-8">
								<div class="input-group">
									<select id="tran_ref_doc_id" class="selectpicker  form-control">
										<option value="Select" selected>Please Select</option>
                                       <?php
																																							while ( $hos_row_ref = mysql_fetch_assoc ( $result_referring_in_query ) ) {
																																								?>
                                       <option
											value='<?php echo $hos_row_ref['Doctor_serial_id'];?>'><?php echo $hos_row_ref['Doctor_name'];?></option>
<?php }?>

									</select> <span class="input-group-btn"> <!-- <button class="btn btn-sm btn-default" type="button">
											<i class=" fa fa-plus"></i>&nbsp; Add
										</button> -->
									</span>
								</div>

							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Hospital Doctor Name</label>
							<div class="col-lg-8">
								<div class="input-group">
									<select id="tran_hos_doc_id" class="selectpicker  form-control">
										<option value="Select" selected>Please Select</option>
                                       <?php
																																							while ( $hos_row_doc = mysql_fetch_assoc ( $result_refer_out_query ) ) {
																																								?>
                                       <option
											value='<?php echo $hos_row_doc['Doctor_serial_id'];?>'><?php echo $hos_row_doc['Doctor_name'];?></option>
<?php }?>

									</select> <span class="input-group-btn"> <!-- <button class="btn btn-sm btn-default" type="button">
											<i class=" fa fa-plus"></i>&nbsp; Add
										</button> -->
									</span>
								</div>

							</div>
						</div>

						<!-- <div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input readonly type="text" class="form-control"
									placeholder="10 digit mobile #">
							</div>
						</div> -->

						
						<!-- <div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input readonly type="text" class="form-control"
									placeholder="10 digit mobile #">
							</div>
						</div> -->

						<div class="form-group">
							<label class="col-lg-4 control-label">Notes:</label>
							<div class="col-lg-8">
								<textarea class="form-control limited" id="tran_notes"
									maxlength="150"></textarea>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger"
						onclick="add_admin_transaction()">Add Transaction</button>
				</div>
			</div>
		</div>
	</div>

	<!-- MOdal Add Mangae users -->
<div class="modal fade" id="addhspuser" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Mangae users</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<label class="col-lg-4 control-label">Hospital User Name</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder=""
									name="user_name" id="user_name" value=""
									onKeyDown="return ValidateAlpha(event);">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="10 digit mobile" name="user_mobile" id="user_mobile"
									value="" maxlength=10
									onKeyPress="return restrictInput(this,event,digitsOnly)">
							</div>
						</div>
						<!-- <div class="form-group">
							<label class="col-lg-4 control-label">User Type</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder="User name">
							</div>
						</div> -->
						<div class="form-group">
							<label class="col-lg-4 control-label">Email ID</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="abc@test.com" name="user_email" id="user_email"
									onblur="validateEmail(this);">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control" name="user_pass1"
									id="user_pass1" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Confirm Password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control" name="user_pass2"
									id="user_pass2" value="">
							</div>
						</div>
						<input type="hidden" name="hospital_id" id="hospital_id"
							value="<?php echo $hospital_id ?>" />
					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger" onclick="add_hospital_user()">Add Hospital User</button>
				</div>
			</div>
		</div>
	</div>	
	<!-- Modal Paient Message list mdMessagae-->
	
	<div class="modal fade" id="mdMessagae" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Patient name</h4>
				</div>
				<div class="modal-body">

					<div class="post">
						<div class="default">
							<h4 class="pink">Patient name</h4>
							<p>Hey Mike...Nullam quis risus eget urna mollis ornare vel eu
								leo. Cum sociis natoque penatibut</p>
							<div class="text-right">
								<span class="pink"> <i class="fa fa-calendar"></i> 22/12/2015
								</span> <span class="pink"> <i class=" fa fa-clock-o "></i>
									16:22
								</span>
							</div>
						</div>
					</div>
					<!-- <div class="post">
						<div class="default">
							<h4 class="pink">Dr. Abc</h4>
							<p>Hey Mike...Nullam quis risus eget urna mollis ornare vel eu
								leo. Cum sociis natoque penatibut</p>
							<div class="text-right">
								<span class="pink"> <i class="fa fa-calendar"></i> 22/12/2015
								</span> <span class="pink"> <i class=" fa fa-clock-o "></i>
									16:22
								</span>
							</div>
						</div>
					</div> -->
					<div class="send">
						<form>
							<textarea class="textarea form-control" rows="5" cols="30"
								placeholder="Enter text ..." id="new_transaction_msg"></textarea>
							<button type="button" class="btn btn-danger btn-send">
								<i class="fa fa-envelope-o "></i>&nbsp;Send
							</button>
						</form>
					</div>

				</div>

			</div>
		</div>
	</div>
	
	
	<!-- modal for profile -->
	<div class="modal fade" id="profile" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Profile</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<label class="col-lg-4 control-label">Name</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" placeholder="name">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Mobile Number</label>
							<div class="col-lg-8">
								<input type="text" class="form-control"
									placeholder="10 digit mobile #">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger">save</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal message display messagemd-->
	<div class="modal fade" id="messagemd" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header ">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Sent Message</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">

						<div class="form-group">

							<div class="col-lg-12">
								<div>
									<span class="date pull-right"><i class="fa fa-clock-o"></i> 20
										Nov 2015 10:55 AM</span>
									<h4 class="from">Jeff Hanneman</h4>
									<p class="msg">Urgent - You forgot your keys in the class room,
										please come imediatly!</p>
								</div>

							</div>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>

	<!-- modal message for diplaying messagsend1 --->
	<div class="modal fade" id="messagsend1" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Sent Message</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">

						<div class="form-group">

							<div class="col-lg-12">

								<div>
									<span class="date pull-right"><i class="fa fa-clock-o"></i> 20
										Nov 2015 10:55 AM</span>
									<h4 class="from">Jeff Hanneman</h4>
									<p class="msg">Urgent - You forgot your keys in the class room,
										please come imediatly!</p>
								</div>

							</div>
						</div>
				
				</div>
				</form>
			</div>

		</div>
	</div>
	</div>

	<!-- modal for setting -->
	<div class="modal fade" id="setting" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Profile</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<label class="col-lg-4 control-label">Old password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control"
									placeholder="old password">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">New password</label>
							<div class="col-lg-8">
								<input type="password" class="form-control"
									placeholder="new password">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-danger">save</button>
				</div>
			</div>
		</div>
	</div>
	<div class=" color">
		<div class="container">

			<ul class=" nav navbar-nav pull-right top-nav">
				<li class="dropdown pull-right "><a data-toggle="dropdown"
					class="dropdown-toggle" href="#" aria-expanded="true"> <i
						class="fa fa-user"></i>&nbsp; Admin <b class="caret"></b>
				</a> <!-- Dropdown menu -->
					<ul class="dropdown-menu">
						<!-- <li><a href="#" data-toggle="modal" data-target="#profile"><i
								class="fa fa-user"></i> Profile</a></li>
						<li><a href="#" data-toggle="modal" data-target="#setting"><i
								class="fa fa-cogs"></i> Settings</a></li> -->
						<li><a href="logout_referralio.php"><i class="fa fa-sign-out"></i>
								Logout</a></li>
					</ul></li>

			</ul>
		</div>
	</div>
	<!-- main logo -->
	<div class="container">
		<div class="row top-padd">
			<div class="col-md-12">

				<img src="img/hos_logo.jpg" class="logo">


			</div>
		</div>
	</div>
	</div>

	<div id="main-nav" class="h-sidebar">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">

			<li role="presentation" class="active"><a href="#view5"
				aria-controls="view5" role="tab" data-toggle="tab" class="tabanchor">Hospital
					Doctors</a></li>
			<li role="presentation"><a href="#view6" aria-controls="view6"
				role="tab" data-toggle="tab" class="tabanchor">Referring Doctors</a></li>
			<li role="presentation"><a href="#view3" aria-controls="view3"
				role="tab" data-toggle="tab" class="tabanchor">Transactions</a></li>
			<li role="presentation"><a href="#view4" aria-controls="view4"
				role="tab" data-toggle="tab" class="tabanchor">Push Notification</a></li>
			<li role="presentation"><a href="#view2" aria-controls="view2"
				role="tab" data-toggle="tab" class="tabanchor">Manage Hospital Users</a></li>
			<li role="presentation"><a href="#view1" aria-controls="view1"
				role="tab" data-toggle="tab" class="tabanchor"> Analytics </a></li>

		</ul>
	</div>
	<!-- Tab panes -->
	<div class="main-container">
		<div class="tab-content">
			<!-- Hospital Doctors Panel -->
			<div role="tabpanel" class="tab-pane active" id="view5">
				<div class="container">

					<div class="row">
						<div class="padd">
							<button class="btn btn-danger pull-right" type="button"
								data-toggle="modal" data-target="#addhspdoctor">
								<i class="fa fa-plus bigger-110"></i> Add Hospital Doctors
							</button>
						</div>
					</div>

					<div class="row">

						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">Hospital Doctors</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<?php
										if ($count_refer_out_query != 0) {
											?>
										<input type="hidden" name="hospital_id" id="hospital_id"
											value="<?php echo $hospital_id ?>" />
										<table
											class="table table-striped table-bordered table-hover data-table">
											<thead>
												<tr>
													<th>Name</th>
													<th>Email</th>
													<th>Mobile #</th>
													<th>Specialization</th>
													<th>Experience(yrs)</th>
													<th>Qualification</th>
													<th>Control</th>
												</tr>
											</thead>
											<tbody>
											    
											    <?php
													mysql_data_seek ( $result_refer_out_query, 0 );
													while ( $row = mysql_fetch_assoc ( $result_refer_out_query ) ) {
												?>

												<tr>

													<td><?php echo $row['Doctor_name'];?></td>
													<td><?php echo $row['Doctor_email'];?></td>
													<td><?php echo $row['Doctor_mobile_number'];?></td>
													<td><?php echo $row['Doctor_specialization'];?></td>
													<td><?php echo $row['Doctor_yxp'];?></td>
													<td><?php echo $row['Doctor_qualification'];?></td>
													<td>

														<!-- <button class="btn btn-xs btn-info">
															<i class=" fa fa-pencil "></i>
														</button>
														<button class="btn btn-xs btn-success">
															<i class=" fa fa-floppy-o"></i>
														</button> -->
														<button class="btn btn-xs btn-danger" id="button_2"
															value="Delete"
															onclick="delete_hospital_doc(<?php echo $row['Doctor_serial_id'];?>)">
															<i class="fa fa-times"></i>
														</button>

													</td>
												</tr>
													<?php
											}
											?>
											</tbody>
										</table>
										
										<?php
										} else {
											echo "No Refer Out Available Yet";
										}
										?>
										
									</div>
								</div>
								<div class="widget-foot">


									<!-- <ul class="pagination pagination-sm pull-right">
										<li><a href="#">Prev</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">Next</a></li>
									</ul> -->

									<div class="clearfix"></div>

								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
			<!-- Referring Doctors panel -->
			<div role="tabpanel" class="tab-pane " id="view6">
				<div class="container">
					<div class="row">
						<div class="padd">
							<button class="btn btn-danger pull-right" type="button"
								data-toggle="modal" data-target="#addrepdoctor">
								<i class="fa fa-plus "></i> Add Referring Doctors
							</button>
						</div>
					</div>
					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">Referring Doctors : Registered</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<input type="hidden" name="hospital_id" id="hospital_id"
											value="<?php echo $hospital_id ?>" />
											<?php
												if ($count_refer_in_query != 0) {
											?>
										<table
											class="table table-striped table-bordered table-hover data-table">
											<thead>
												<tr>
													<th>Doctor Name</th>
													<th>Doctor Email</th>
													<th>Doctor Mobile Number</th>
													<th>Doctor Specialization</th>
													<th>Doctor experience(yrs)</th>
													<th>Doctor Qualification</th>
													<th>Action</th>
												<tr>
											</thead>
											<tbody>
												<?php
											mysql_data_seek ( $result_referring_in_query, 0 );
											while ( $row = mysql_fetch_assoc ( $result_referring_in_query ) ) {
												?>
												<tr>
													<td><?php echo $row['Doctor_name'];?></td>
													<td><?php echo $row['Doctor_email'];?></td>
													<td><?php echo $row['Doctor_mobile_number'];?></td>
													<td><?php echo $row['Doctor_specialization'];?></td>
													<td><?php echo $row['Doctor_yxp'];?></td>
													<td><?php echo $row['Doctor_qualification'];?></td>

													<td>
														<!-- <button class="btn btn-xs btn-info">
															<i class=" fa fa-pencil "></i>
														</button>
														<button class="btn btn-xs btn-success">
															<i class=" fa fa-floppy-o"></i>
														</button> -->

														<button class="btn btn-xs btn-danger" id="button_2"
															value="Delete" onclick="delete_referring_doc(<?php echo $row['Doctor_serial_id'];?>)">
															<i class="fa fa-times"></i>
														</button>

													</td>
												</tr>
												<?php
											}
											
											?>
											</tbody>
										</table>
										<?php
										} else {
											?>
				     <div class="pull-left widgehead" style="color: red">No Refer In
											Doctors available yet</div>
				   <?php
										}
										?>
									</div>
								</div>
								<div class="widget-foot">


									<!-- <ul class="pagination pagination-sm pull-right">
										<li><a href="#">Prev</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">Next</a></li>
									</ul> -->

									<div class="clearfix"></div>

								</div>

							</div>

						</div>
					</div>
					
					
					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">Referring Doctors : Pending</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<input type="hidden" name="hospital_id" id="hospital_id"
											value="<?php echo $hospital_id ?>" />
											<?php
												if ($count_pending_refer_in != 0) {
											?>
										<table
											class="table table-striped table-bordered table-hover data-table">
											<thead>
												<tr>
													<th>Doctor Name</th>
													<th>Doctor Email</th>
													<th>Doctor Mobile Number</th>
													<!-- <th>Doctor Specialization</th>
													<th>Doctor experience(yrs)</th>
													<th>Doctor Qualification</th> -->
													<th>Time Added</th>
												<tr>
											</thead>
											<tbody>
												<?php
											mysql_data_seek ( $result_pending_referring_doctors, 0 );
											while ( $row = mysql_fetch_assoc ( $result_pending_referring_doctors ) ) {
												?>
												<tr>
													<td><?php echo $row['refer_in_doc_name'];?></td>
													<td><?php echo $row['refer_in_doc_email'];?></td>
													<td><?php echo $row['refer_in_doc_mobile'];?></td>
													<!-- <td><?php echo $row['Doctor_specialization'];?></td>
													<td><?php echo $row['Doctor_yxp'];?></td>
													<td><?php echo $row['Doctor_qualification'];?></td>-->
													<td><?php echo $row['refer_by_time'];?></td>

													<!-- <td>
														<button class="btn btn-xs btn-info">
															<i class=" fa fa-pencil "></i>
														</button>
														<button class="btn btn-xs btn-success">
															<i class=" fa fa-floppy-o"></i>
														</button>

														<button class="btn btn-xs btn-danger" id="button_2"
															value="Delete" onclick="delete_referring_doc(<?php echo $row['Doctor_serial_id'];?>)">
															<i class="fa fa-times"></i>
														</button>

													</td>-->
												</tr>
												<?php
											}
											
											?>
											</tbody>
										</table>
										<?php
										} else {
											?>
				     <div class="pull-left widgehead" style="color: red">No Refer In
											Doctors available yet</div>
				   <?php
										}
										?>
									</div>
								</div>
								<div class="widget-foot">


									<!-- <ul class="pagination pagination-sm pull-right">
										<li><a href="#">Prev</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">Next</a></li>
									</ul> -->

									<div class="clearfix"></div>

								</div>

							</div>

						</div>
					</div>

				</div>
			</div>
			<!-- Transactions panel -->
			<div role="tabpanel" class="tab-pane" id="view3">
				<div class="container">
					<div class="row">
						<div class="padd">
							<button class="btn btn-danger pull-right" type="button"
								data-toggle="modal" data-target="#addTransactions">
								<i class="fa fa-plus bigger-110"></i> Add Transactions Doctors
							</button>
						</div>
					</div>

					<div class="row">

						<div class="widget">

							<div class="widget-head">
								<div class="pull-left widgehead">Transactions</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<?php
										if ($count_result_transactions != 0) {
											?>
										<table
											class="table table-striped table-bordered table-hover data-table">
											<thead>
												<tr>

													<th>Date & Time</th>
													<th>Patient</th>
													<th>Referring Doctor</th>
													<th>Hospital Doctor</th>
													<!-- <th>Referring Doctor</th> -->
													<th>Primary Status</th>
													<th>Secondary Status</th>
													<th>Created By</th>
												</tr>
											</thead>
											<tbody>
											<?php
											while ( $row = mysql_fetch_assoc ( $result_transactions ) ) {
												?>
											

												<tr>
													<td><?php echo $row['Timestamp'];?></td>
													<!-- <td><a href="" data-toggle="modal"
														data-target="#mdMessagae"
														id='<?php echo $row['Patient_thread_id'];?>'><?php echo $row['Patient_Name']."( ".$row['Patient_mobile_number']." )";?></a></td>-->
													<td><a href="" class="transaction-patient-link" id='<?php echo $row['Patient_thread_id'];?>'><?php echo $row['Patient_Name']."( ".$row['Patient_mobile_number']." )";?></a></td>	
													<td><?php echo $row['reg_by_doc_Doctor_name'].'('.$row['reg_by_doc_Doctor_mobile_number'].')';?></td>
													<td><?php echo $row['doc_ref_id_Doctor_name'].'('.$row['doc_ref_id_Doctor_mobile_number'].')';?></td>
													<?php if($row['primary_status'] == 0){
													?>
													<td><span class="label label-success1 ">New</span></td>
													<?php 
												}else{
													?>
													<td><span class="label label-success ">Responded</span></td>
													<?php }?>

													<td><select name="status" id="status_transaction" class="form-control" onchange="change_secondary(this,<?php echo $row['Patient_thread_id'];?>)">
															<option name="Select" value="Select" selected>Select</option>
															<option name="Visited" value="Visited"<?php if ($row['secondary_status'] == 'Visited') echo ' selected="selected"'; ?>>Visited</option>
															<option name="Treated" value="Treated"<?php if ($row['secondary_status'] == 'Treated') echo ' selected="selected"'; ?>>Treated</option>
															<option name="Discharged" value="Discharged"<?php if ($row['secondary_status'] == 'Discharged') echo ' selected="selected"'; ?>>Discharged</option>
														</select>
													</td>
													
													<?php if($row['created_by'] == 'Mobile Application'){
													?>
													<td><span class="label label-created "><?php echo $row['created_by'];?></span></td>
													<?php 
												}else{
													?>
													<td><span class="label label-created1 "><?php echo $row['created_by'];?></span></td>
													<?php }?>
												</tr>
<?php
											}
											
											?>
											</tbody>
										</table>
										<?php
										} else {
											echo "No Transactions Available Yet";
										}
										?>
									</div>

									<div class="widget-foot">


										<!-- <ul class="pagination pagination-sm pull-right">
											<li><a href="#">Prev</a></li>
											<li><a href="#">1</a></li>
											<li><a href="#">2</a></li>
											<li><a href="#">3</a></li>
											<li><a href="#">4</a></li>
											<li><a href="#">Next</a></li>
										</ul> -->

										<div class="clearfix"></div>

									</div>

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Messaging panel -->
			<div role="tabpanel" class="tab-pane" id="view4">
				<div class="container">
					<div class="row">
						<div class="padd"></div>
					</div>
					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">Messaging</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>

							<div class="widget-content">
								<div class="padd">
									<div class="row">
										<!-- message widget -->
										<div class="col-md-6">
											<!-- Widget -->
											<div class="widget">
												<div class="widget-head">
													<div class="pull-left widgehead">Message to all Referring
														doctors</div>

													<div class="clearfix"></div>
												</div>
												<div class="widget-content">
													<div class="padd">

														<form class="form">
															<textarea class="form-control" rows="5"
																placeholder="Textarea" name="msg_refer_in"
																id="msg_refer_in" value=""></textarea>

															<input type="hidden" name="hospital_id" id="hospital_id"
																value="<?php echo $hospital_id ?>" />

															<div class="clearfix"></div>
															<div class="buttons">
																<button class="btn btn-sm btn-danger"
																	onClick="sendB2B_referin()">Send</button>
															</div>
														</form>

													</div>

												</div>
											</div>
											<div class="clearfix"></div>
											<!-- <div class="mail-inbox">
												<div class="head">
													<h3>Sent Message</h3>
												</div>
												
												<?php
												$cnt_refer_in_msgs=mysql_num_rows($res_query_get_refer_in_msgs);
												if($cnt_refer_in_msgs>0){
													while($row_refer_in_msgs=mysql_fetch_assoc($res_query_get_refer_in_msgs))
													{
												?>

												<div class="mails">
													<div class="item" data-toggle="modal"
														data-target="#messagemd">

														<div>
															<span class="date pull-right"><i class="fa fa-clock-o"></i>
																<?php echo $row_refer_in_msgs['datetime'];?></span>
															<h4 class="from"><?php echo $row_refer_in_msgs['Doctor_name'] ?></h4>
															<p class="msg"><?php echo $row_refer_in_msgs['message_content']?></p>
														</div>
													</div>
												</div>
												<?php }
												}
												else{
													echo "No Messaages available";
												}
												?>
											</div>-->
										</div>
										<div class="col-md-6">
											<!-- Widget -->
											<div class="widget">
												<div class="widget-head ">
													<div class="pull-left widgehead">Message to all Hospital
														doctors</div>

													<div class="clearfix"></div>
												</div>
												<div class="widget-content">
													<div class="padd">

														<form class="form">
															<textarea class="form-control" rows="5"
																placeholder="Textarea" name="msg_refer_out"
																id="msg_refer_out" value=""></textarea>

															<div class="clearfix"></div>
															<div class="buttons">
																<button class="btn btn-sm btn-danger"
																	onclick="sendB2B_referout()">Send</button>
															</div>
														</form>

													</div>

												</div>
											</div>
											<div class="clearfix"></div>
											<!-- <div class="mail-inbox">
												<div class="head">
													<h3>Sent Message</h3>
												</div>
												
												<?php
												$cnt_refer_out_msgs=mysql_num_rows($res_query_get_refer_out_msgs);
												if($cnt_refer_out_msgs>0){
													while($row_refer_out_msgs=mysql_fetch_assoc($res_query_get_refer_out_msgs))
													{
												?>

												<div class="mails">
													<div class="item" data-toggle="modal"
														data-target="#messagemd">

														<div>
															<span class="date pull-right"><i class="fa fa-clock-o"></i>
																<?php echo $row_refer_out_msgs['datetime'];?></span>
															<h4 class="from"><?php echo $row_refer_out_msgs['Doctor_name'] ?></h4>
															<p class="msg"><?php echo $row_refer_out_msgs['message_content']?></p>
														</div>
													</div>
												</div>
												<?php }
												}
												else{
													echo "No Messaages available";
												}
												?>
											</div>-->
										</div>
									</div>
								</div>






							</div>


						</div>
					</div>


				</div>

			</div>


			<div role="tabpanel" class="tab-pane" id="view2">
				<div class="container">
					<div class="row">
						<div class="padd">
							<button class="btn btn-danger pull-right" type="button"
								data-toggle="modal" data-target="#addhspuser">
								<i class="fa fa-plus "></i> Add Manage Hospital Users
							</button>
						</div>
					</div>
					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">View/Add Hospital Users</div>
								<div class="widget-icons pull-right">
									<a href="#" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<?php
        if ($count_hospital_users != 0) {
            ?>
										<table
											class="table table-striped table-bordered table-hover data-table"
											style="height: auto">
											<thead>
												<tr>
													<th>Name</th>
													<th>Email</th>
													<th>Mobile #</th>
													<th>Referring Doctor</th>
													<!-- <th>Control</th> -->


												</tr>
											</thead>



											<tbody>
											<?php
            while ( $row_hospital_user = mysql_fetch_assoc ( $result_hospital_users ) ) {
                ?>

												<input type="hidden" name="stub_id" id="stub_id"
													value="<?php echo $row_hospital_user['Doctor_serial_id'];?>" />
												<input type="hidden" name="hospital_id" id="hospital_id"
													value="<?php echo $hospital_id ;?>" />
												<tr>
													<td><?php echo $row_hospital_user['Doctor_name']; ?></td>
													<td><?php echo $row_hospital_user['Doctor_email']; ?></td>
													<td><?php echo $row_hospital_user['Doctor_mobile_number']; ?></td>
													<td><select
														onChange="add_referring_2_hospital_user(<?php echo $row_hospital_user['Doctor_serial_id'];?>,<?php echo $hospital_id ;?>,this)"
														name="refer_select" id="refer_select">
															<option value="Select">Select</option>
																<?php
                mysql_data_seek ( $result_referring_in_query, 0 );
                while ( $row_referring_in = mysql_fetch_assoc ( $result_referring_in_query ) ) {
                    
                    ?>
																<option
																value="<?php echo $row_referring_in['Doctor_serial_id']?>"><?php echo $row_referring_in['Doctor_name']?></option>
																<?php
                
}
                ?>
															

													</select></td>

													<!-- <td>

														<button class="btn btn-xs btn-info">
															<i class=" fa fa-pencil "></i>
														</button>
														<button class="btn btn-xs btn-success">
															<i class=" fa fa-floppy-o"></i>
														</button>
														<button class="btn btn-xs btn-danger" id="button_2"
															value="Delete" onclick="">
															<i class="fa fa-times"></i>
														</button>

													</td>-->
												</tr>
												
												<?php } ?>

											</tbody>
										</table>
										<?php
        
} else {
            echo "No hospital Users yet";
        }
        ?>
									</div>
								</div>
								<!-- <div class="widget-foot">


									<ul class="pagination pagination-sm pull-right">
										<li><a href="#">Prev</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">Next</a></li>
									</ul>

									<div class="clearfix"></div>

								</div> -->

							</div>

						</div>
					</div>
					
					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">View/Delete Mapped Hospital Users</div>
								<div class="widget-icons pull-right">
									<a href="#" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
										<!-- <div id="" class="dataTables_filter">
											<label>Search:<input type="search" class="" placeholder=""
												aria-controls="data-table-1"></label>
										</div> -->
										<?php
        if ($count_hospital_users != 0) {
            ?>
										<table
											class="table table-striped table-bordered table-hover data-table"
											style="height: auto">
											<thead>
												<tr>
													<th>Name</th>
													<th>Mapped Referring Doctors</th>
													<!-- <th>Control</th> -->


												</tr>
											</thead>



											<tbody>
											<?php
											mysql_data_seek ( $result_hospital_users, 0 );
            while ( $row_hospital_user = mysql_fetch_assoc ( $result_hospital_users ) ) {
                ?>

												<input type="hidden" name="stub_id" id="stub_id"
													value="<?php echo $row_hospital_user['Doctor_serial_id'];?>" />
												<input type="hidden" name="hospital_id" id="hospital_id"
													value="<?php echo $hospital_id ;?>" />
												<tr>
													<td width="45%"><?php echo $row_hospital_user['Doctor_name']; ?></td>
													<?php
													
													$id_doc=$row_hospital_user['Doctor_serial_id'];
													
													$query_get_refer_mapped_doc="SELECT * FROM referral_mapping rm inner join doctor_stub ds on rm.referring_doctor_id=ds.Doctor_serial_id where hospital_user_id='$id_doc'";
													
													$res_get_refer_mapped=mysql_query($query_get_refer_mapped_doc);
													mysql_num_rows($res_get_refer_mapped);
													
													?>
													<td><select
													onChange="delete_referring_2_hospital_user(<?php echo $id_doc;?>,<?php echo $hospital_id ;?>,this)"
													        name="refer_select" id="refer_select" style="width:120px">
													        
													        <option value="Select">Select</option>
												    <?php
												    //mysql_data_seek ( $res_get_refer_mapped, 0 );
													while($row_get_refer_mapped=mysql_fetch_assoc($res_get_refer_mapped))
													{	
													?>
																<option value="<?php echo $row_get_refer_mapped['Doctor_serial_id']; ?>"><?php echo $row_get_refer_mapped['Doctor_name']; ?></option>
																
													<?php 
													}
													?>

													</select>&nbsp;&nbsp;&nbsp;(<b>Select from the dropdown to delete the mapping</b>)</td>
													

													<!-- <td>

														<button class="btn btn-xs btn-info">
															<i class=" fa fa-pencil "></i>
														</button>
														<button class="btn btn-xs btn-success">
															<i class=" fa fa-floppy-o"></i>
														</button>
														<button class="btn btn-xs btn-danger" id="button_2"
															value="Delete" onclick="">
															<i class="fa fa-times"></i>
														</button>

													</td>-->
												</tr>
												
												<?php } ?>

											</tbody>
										</table>
										<?php
        
} else {
            echo "No hospital Users yet";
        }
        ?>
									</div>
								</div>
								<!-- <div class="widget-foot">


									<ul class="pagination pagination-sm pull-right">
										<li><a href="#">Prev</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">Next</a></li>
									</ul>

									<div class="clearfix"></div>

								</div> -->

							</div>

						</div>
					</div>

					</div>

			</div>

			<div role="tabpanel" class="tab-pane" id="view1">
				<div class="container">

					<div class="row">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left widgehead">Analytics for Referring Doctors</div>
								<div class="widget-icons pull-right">
									<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="widget-content">
								<div class="page-tables">
									<div class="table-responsive">
									
									<?php
					                          //echo  mysql_num_rows($result_referring_in_analytics);
									         mysql_data_seek ( $result_referring_in_analytics, 0 );
											 if(mysql_num_rows($result_referring_in_analytics)!=0){
											 ?>
									
										<div style="width: 55%; float:left"> 
										   <table
											class="table table-striped table-bordered table-hover data-table">
											<thead>
												<tr>
													<th>Name of referring Doctors</th>
													<th>No of Referrals</th>
													<th>Trend</th>
													<!-- <th>Charts</th> -->


												</tr>
												
												
											</thead>
											<?php
										  		while ($row=mysql_fetch_assoc($result_referring_in_analytics)){
											  ?>
											<tbody>
											
												<tr>
													<td><?php echo $row['Doctor_name'];?></td>
													<td><?php echo $row['cnt'];?></td>
													<td>trend1</td>
													<!-- <td><div id="chartContainer" align='center'>FusionCharts XT will load here!</div></td> -->



												</tr>
												
												  
					 <?php   
					  }
					   
					 
					  ?>






											</tbody>
										</table>
										</div>
										
										<div id="chartContainer" align='center' style="float: right">FusionCharts XT will load here!</div>
										 <?php
				 }else{
				   echo "No Referring Doctors Analysis yet";
				 }
				?>
									</div>
								</div>


							</div>

						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="widget">
									<div class="widget-head">
										<div class="pull-left widgehead">Analytics for Hospital
											Doctors</div>
										<div class="widget-icons pull-right">
											<a href="referralio_home.php" class="wminimize"><i class="fa fa-refresh"></i>Reload</a>

										</div>
										<div class="clearfix"></div>
									</div>
									<div class="widget-content">
										<div class="page-tables">
											<div class="table-responsive">
											
											<?php
					                          //echo  mysql_num_rows($result_referring_in_analytics);
											 mysql_data_seek ( $result_hospital_doc_analytics, 0 );
											 if(mysql_num_rows($result_hospital_doc_analytics)!=0){
											 ?>
									
											
											<div style="width: 55%; float:left">
											 <table
													class="table table-striped table-bordered table-hover data-table">
													<thead>
														<tr>
															<th>Name of referring Doctors</th>
															<th># of Referrals</th>
															<th>Trend</th>
															<!-- <th rowspan="2">Charts</th> -->


														</tr>
													</thead>
													
													<?php
													
										  		while ($row=mysql_fetch_assoc($result_hospital_doc_analytics)){
											  ?>
													<tbody>

														<tr>
															<td><?php echo $row['Doctor_name'];?></td>
															<td><?php echo $row['cnt'];?></td>
															<td>trend1</td>
															<!-- <td><div id="chartContainer1" align='center'>FusionCharts XT will load here!</div></td> -->



														</tr>
																			

 <?php   
					  }
					   
					 
					  ?>




													</tbody>
												</table>
											</div>
												
												<div id="chartContainer1" align='center'  style="float:right">FusionCharts XT will load here!</div>
												 <?php
				 }else{
				   echo "No Referring Doctors Analysis yet";
				 }
				?>
											</div>
										</div>
									</div>

								</div>

							</div>
						</div>

					</div>

				</div>


			</div>
		</div>
		
<script type="text/javascript">
    $(document).ready(function() {
      $('a.transaction-patient-link').on('click', function() {
        // this is how you access the patient id that you specified
        var patientId = $(this).attr("id");
        var hospital_id=document.getElementById('hospital_id').value;
        //alert(patientId);
        //alert(hospital_id);
        //return false;
        // build your ajax url using the patient id
        var url = "getTransactionMessages.php?patient=" + patientId+"&hospital="+hospital_id;
        $.get(url, function(data) {
          //alert(data);
          $("#transaction-patient-info").html(data).modal('show');
        });
        return false;
      });
    });
</script>		
		

</body>

<!-- jquery plugins  -->
<script src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.js"></script>

<script>
   $('#main-nav a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})

$("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('#main-nav a[href="' + hash + '"]').tab('show');
</script>
</html>