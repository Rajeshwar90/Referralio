<?php
session_start();
include 'db_conn.php';
			$logger = new Logger();
			$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
			$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);
            $img_path="/profile_doc_images/";
			$Doctor_name = mysql_real_escape_string($_POST['Doctor_name']);		
			$Doctor_dob = $_POST['Doctor_dob'];
			$Doctor_email= mysql_real_escape_string($_POST['Doctor_email']);
			$Doctor_specialization = mysql_real_escape_string($_POST['Doctor_specialization']);
			$Doctor_qualification = mysql_real_escape_string($_POST['Doctor_qualification']);
			$Doctor_HospitalName = mysql_real_escape_string($_POST['Doctor_HospitalName']);
			$Doctor_mobile_number = $_POST['Doctor_mobile_number'];
			$Doctor_password = mysql_real_escape_string($_POST['Doctor_password']);
			$Doctor_photograph=$_POST['Doctor_photograph'];
			$Doctor_yxp=mysql_real_escape_string($_POST['Doctor_yxp']);
			$Doctor_Country=mysql_real_escape_string($_POST['Doctor_Country']);
			$Doctor_State=mysql_real_escape_string($_POST['Doctor_State']);
			$Doctor_City=mysql_real_escape_string($_POST['Doctor_City']);
			$Doctor_Address=mysql_real_escape_string($_POST['Doctor_Address']);
			$license_number=mysql_real_escape_string($_POST['license_number']);
			$Hospital_id=mysql_real_escape_string($_POST['Hospital_id']);
			
			$logger->write("INFO :","POST data mobile".$_POST['Doctor_mobile_number']);

   $unique_doc_query="select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number'";
   $doctor_id_present="";
     $logger->write("INFO :"," not unique data *******************************");
	  while($row=mysql_fetch_assoc($result))
	    {
		   $doctor_id_present=$row['Doctor_serial_id'];
		}
	  $logger->write("INFO :"," doc id present is *******************************".$doctor_id_present);
           $logger->write("INFO :","not unique data *******************************");
               if($Doctor_photograph!=""){
					
					$data = str_replace('data:image/png;base64,', '', $Doctor_photograph);
                    $data = str_replace(' ', '+', $data);
					$data = base64_decode($data);
					$imageName=$Doctor_mobile_number . '.png';
					$file = '../dual_referral/profile_doc_images/'. $Doctor_mobile_number . '.png';
                    //$success = file_put_contents($file, $data);
				}else{
					$imageName = "account_image.png";
				}
				
				if(!empty($Doctor_mobile_number) and !empty($Doctor_password) and !empty($Doctor_name) and !empty($Doctor_dob) and !empty($Doctor_email)){	
			        
					// code to check for uniqueness of mobile number
					$sql_mob_unique=mysql_query("select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number'");
					if(mysql_num_rows($sql_mob_unique)==0)
					{
					  
					    $query_test="insert into doctor_stub (Doctor_Title,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,my_type,country_code) values('$doctor_title','$Doctor_name','$Doctor_dob','$Doctor_email','$Doctor_specialization','$Doctor_qualification','$Doctor_HospitalName','$Doctor_Country','$Doctor_State','$Doctor_City','$Doctor_Address','$Doctor_mobile_number',md5('$Doctor_password'),'$imageName','$Doctor_yxp','$license_number','$visibility','$my_type','$country_code')";
                        
						$logger->write("INFO query:",$query_test);
			        
						$sql = mysql_query("insert into doctor_stub (Doctor_Title,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,my_type,country_code) values('$doctor_title','$Doctor_name','$Doctor_dob','$Doctor_email','$Doctor_specialization','$Doctor_qualification','$Doctor_HospitalName','$Doctor_Country','$Doctor_State','$Doctor_City','$Doctor_Address','$Doctor_mobile_number',md5('$Doctor_password'),'$imageName','$Doctor_yxp','$license_number','$visibility','$my_type','$country_code')");
						
						$doc_id = mysql_insert_id();
						
						$insert_hospital_refer_out="INSERT INTO hospital_refer_out_doctor_stub(doctor_stub_id, hospital_id) VALUES ('$doc_id','$Hospital_id')";
	  
	                    $result_insert_refer_out=mysql_query($insert_hospital_refer_out);
						
						/*if(mysql_num_rows($sql) > 0){
							$result = mysql_fetch_array($sql,MYSQL_ASSOC);
							
							// If success everythig is good send header as "OK" and user details
							$this->response($this->json($result), 200);
						}
						$this->response('', 204);	// If no records "No Content" status
					   */
					   if($sql=='success' && $result_insert_refer_out=='success'){
					        
							if($Doctor_photograph!=""){
							  $logger->write("INFO inside file image putting");
							  $file_put = file_put_contents($file, $data);
							}
							
							$success = array('status' => "Success", "msg" => "Successfully doctor has been registered");
							//$this->response($this->json($success),200);
							echo json_encode($success);
	                        exit(0);
						 }
					  else
						 {
							$success = array('status' => "Failed", "msg" => "SQL query failed");
							//$this->response($this->json($error), 400);
							echo json_encode($success);
	                        exit(0);
						 }
				
			     }
				 
				 else
				 {
				    $success = array('status' => "Failed", "msg" => "A user already registered with this mobile number");
					//$this->response($this->json($error), 400);
					echo json_encode($success);
	                exit(0);
				 }
				 
			
			
			
			}
			else
			{
			  // If invalid inputs "Bad Request" status message and reason
			  $success = array('status' => "Failed", "msg" => "Invalid inputs provided");
			  //$this->response($this->json($error), 400);
			  echo json_encode($success);
	          exit(0);
			}
   
   
   }
echo json_encode($success);
   function send_msg($mobile,$mobile_msg){


?>