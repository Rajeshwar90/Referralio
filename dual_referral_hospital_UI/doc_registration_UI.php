<?php
session_start();if(!isset($_SESSION['hospital_id_SESSION'])){  header("Location: hospital_login.php");  die();}$hospital_id = $_SESSION ['hospital_id_SESSION'];$hospital_name = $_SESSION ['hospital_name_SESSION'];
include 'db_conn.php';include 'logger.php';include 'Email_sender.php';
			$logger = new Logger();			$email_sender=new EMAIL_SENDER();		    
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
			$license_number=mysql_real_escape_string($_POST['license_number']);			$my_type=mysql_real_escape_string($_POST['type']);			$country_code=mysql_real_escape_string($_POST['country_code']);			$visibility=1;
			$Hospital_id=mysql_real_escape_string($_POST['Hospital_id']);						$doctor_title=mysql_real_escape_string($_POST['doctor_title']);						$imageName = "account_image.png";
			
			$logger->write("INFO :","POST data mobile".$_POST['Doctor_mobile_number']);			$logger->write("INFO :","POST data ".$_POST);

   $unique_doc_query="select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number'";   $result=mysql_query($unique_doc_query);   $count_unique=mysql_num_rows($result);
   $doctor_id_present="";   if($count_unique>0){
     $logger->write("INFO :"," not unique data *******************************");
	  while($row=mysql_fetch_assoc($result))
	    {
		   $doctor_id_present=$row['Doctor_serial_id'];
		}
	  $logger->write("INFO :"," doc id present is *******************************".$doctor_id_present);	  	  	  	  	  //condition to restrict adding hospital doctor in two or more hospitals	  $res_query_unique_referout_any=mysql_query("select * from hospital_refer_out_doctor_stub where doctor_stub_id='$doctor_id_present' and hospital_id !='$Hospital_id' and hospital_id!= ''");	   	  $count_query_unique_referout_any=mysql_num_rows($res_query_unique_referout_any);	  	  $logger->write("INFO :"," count_query_unique_referout_any*******************************".$count_query_unique_referout_any);	   	  if($count_query_unique_referout_any==0){	      //condition to check if the doctor has been added in the same hospital	      $res_query_unique_referout=mysql_query("select * from hospital_refer_out_doctor_stub where doctor_stub_id='$doctor_id_present' and hospital_id='$Hospital_id'");	      $count_query_unique_referout=mysql_num_rows($res_query_unique_referout);	      $logger->write("INFO :"," count_query_unique_referout*******************************".$count_query_unique_referout);	      if($count_query_unique_referout==0){	      	           	          //updating the hospital name for the doctor id	          	          $query_gethos_name=mysql_query("select * from hospital_stub where hospital_id='$Hospital_id'");	          $row_get_hos=mysql_fetch_assoc($query_gethos_name);	          $hospital_name=$row_get_hos['hospital_name'];	          	          $update_hos_name=mysql_query("update doctor_stub set Doctor_HospitalName='$hospital_name' where Doctor_Serial_id='$doctor_id_present'");	      	      	          $insert_hospital_refer_out="INSERT INTO hospital_refer_out_doctor_stub(doctor_stub_id, hospital_id) VALUES ('$doctor_id_present','$Hospital_id')";	      	          $logger->write("INFO :"," count_query_unique_referout".$insert_hospital_refer_out);	      	          $result_insert_refer_out=mysql_query($insert_hospital_refer_out);	          	          	          //for handing temp doctors that is added as a referring doctor before the doctor registers	          $logger->write("INFO :"," before handling temp doctors");	          $temp='temp';	          $doc_unregister='False';	          $sql_mob_unique_temp=mysql_query("select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number' and Doctor_unregistered='$temp'");	          $logger->write("INFO :","count handling temp doctors".mysql_num_rows($sql_mob_unique_temp));	          if(mysql_num_rows($sql_mob_unique_temp)>0)	          {	              $res_sql_mob=mysql_fetch_assoc($sql_mob_unique_temp);	              $doc_ser_id=$res_sql_mob['Doctor_serial_id'];	               	              $update_doctor=mysql_query("update doctor_stub set Doctor_name='$Doctor_name',Doctor_dob='$Doctor_dob',Doctor_email='$Doctor_email',Doctor_specialization='$Doctor_specialization',Doctor_qualification='$Doctor_qualification',Doctor_HospitalName='$Doctor_HospitalName',Doctor_Country='$Doctor_Country',Doctor_State='$Doctor_State',Doctor_City='$Doctor_City',Doctor_Address='$Doctor_Address',Doctor_password=md5('$Doctor_password'),Doctor_photograph='$imageName',Doctor_yxp='$Doctor_yxp',license_number='$license_number',visibility='$visibility',my_type='$my_type',country_code='$country_code',Doctor_unregistered='$doc_unregister' where Doctor_mobile_number='$Doctor_mobile_number'");	          	              $update_refer_in=mysql_query("update hospital_refer_in_doctor_stub set doc_stub_id='$doc_ser_id' where refer_in_doc_mobile='$Doctor_mobile_number'");	          	              $logger->write("INFO :","updated doctor stub with doc info and hospital_refer_in_doctor_stub with doc serial id");	              //$success = array('status' => "Success", "msg" => "Successfully doctor has been registered");	              //$this->response($this->json($success),200);	          }	          	           	           	          //sending text message to registered doctor_id_present	          $link="https://play.google.com/store/apps/details?id=com.hospitalcheck.referralio&hl=en";	          $mobile_txt_msg=$hospital_name." - Dear ".$doctor_title.$Doctor_name." You have been added as a inhouse doctor by ".$Doctor_HospitalName." hospital.Please use this below link to download the app: ".$link;	          //$logger->write("calling mobile msg");	          send_msg($Doctor_mobile_number,$mobile_txt_msg);	      	          //sending email if doctor email is present	          if($Doctor_email!=''){	              $email_sender->send_email($Doctor_email,$mobile_txt_msg);	          }	          	      	          $success = array('status' => "Success", "msg" => "Successfully doctor has been registered");	      	          //$this->response($this->json($success),200);	      	          echo json_encode($success);	      	          exit(0);	      	           	      	      }else{	      	      	      	          $success = array('status' => "Failure", "msg" => "Doctor already added in this hospital");	      	          //$this->response($this->json($success),200);	      	          echo json_encode($success);	      	          exit(0);	      	           	      	      }	  }else{	      	      $success = array('status' => "Failed", "msg" => "Doctor already added as a hospital doctor in another hospital");	      	      //$this->response($this->json($error), 400);	      	      echo json_encode($success);	      	      exit(0);	      	       	  }   }else{
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
							//$this->response($this->json($success),200);														//sending text message to registered doctor_id_present(not modified in testing server yet)							  $link="https://play.google.com/store/apps/details?id=com.hospitalcheck.referralio&hl=en";							  $mobile_txt_msg=$hospital_name." - Dear ".$doctor_title.$Doctor_name." You have been added as a Hospital doctor of ".$Doctor_HospitalName." hospital";							  							  $link_txt_msg="Please use this below link to download the app: ".$link;							  							  $logger->write("calling mobile msg");							  send_msg($Doctor_mobile_number,$mobile_txt_msg);							  send_msg($Doctor_mobile_number,$link_txt_msg);							  							  $mobile_txt_msg1=$hospital_name." - Your username is ".$Doctor_mobile_number. " and password is ". $Doctor_password;							  $logger->write("calling mobile msg".$mobile_txt_msg1);							  send_msg($Doctor_mobile_number,$mobile_txt_msg1);	  							 //sending email to registered doctor							  $email_msg=$hospital_name." - Dear ".$doctor_title.$Doctor_name." You have been added as a doctor by hospital ".$Doctor_HospitalName." with username and password ".$Doctor_mobile_number.",".$Doctor_password." respectively";							  							  //$logger->write("calling email sender");							  $email_sender->send_email($Doctor_email,$email_msg.$link_txt_msg);
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
   function send_msg($mobile,$mobile_msg){		    			//$logger->write("INFO :","login with mobile".$mobile);		    			$msg="";									$msg=urlencode($mobile_msg);			  									//$logger->write("INFO :","login with msg".$msg);			$url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;						//$logger->write("INFO :","login with url".$url);            			$ch = curl_init();  // setup a curl			curl_setopt($ch, CURLOPT_URL, $url);  // set url to send to			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 			$output=curl_exec($ch);           //echo "output".$output;			curl_close($ch);			//return $output;		} 


?>