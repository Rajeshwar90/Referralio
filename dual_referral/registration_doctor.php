<?php
   	
	require_once("Rest.inc.php");
	
	class REGISTRATION_DOCTOR extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "refSqlRef007";
		const DB = "referralapp";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();			// Initiate Database connection
			include_once("logger.php");
			$this->logger = new Logger();
			$this->logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
			$this->logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);
		}
		
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['request'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
		/* 
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER MOBILE NUMBER>
		 *  pwd : <USER PASSWORD>
		 */
		
		private function REG_DOC_STUB(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling registration for doctor"); 
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			
			$img_path="/profile_doc_images/";
			$Doctor_name = mysql_real_escape_string($post['Doctor_name']);		
			$Doctor_dob = $post['Doctor_dob'];
			$Doctor_email= mysql_real_escape_string($post['Doctor_email']);
			$Doctor_specialization = mysql_real_escape_string($post['Doctor_specialization']);
			$Doctor_qualification = mysql_real_escape_string($post['Doctor_qualification']);
			$Doctor_HospitalName = mysql_real_escape_string($post['Doctor_HospitalName']);
			$Doctor_mobile_number = $post['Doctor_mobile_number'];
			$Doctor_password = mysql_real_escape_string($post['Doctor_password']);
			$Doctor_photograph=$post['Doctor_photograph'];
			$Doctor_yxp=mysql_real_escape_string($post['Doctor_yxp']);
			$Doctor_Country=mysql_real_escape_string($post['Doctor_Country']);
			$Doctor_State=mysql_real_escape_string($post['Doctor_State']);
			$Doctor_City=mysql_real_escape_string($post['Doctor_City']);
			$Doctor_Address=mysql_real_escape_string($post['Doctor_Address']);
			$visibility=mysql_real_escape_string($post['visibility']);//either 0 or 1
			$license_number=mysql_real_escape_string($post['license_number']);
			$my_type=mysql_real_escape_string($post['my_type']);
			$country_code=mysql_real_escape_string($post['country_code']);
			
			//change for saving doctor image
			if($Doctor_photograph!=""){
					
					$data = str_replace('data:image/png;base64,', '', $Doctor_photograph);
                    $data = str_replace(' ', '+', $data);
					$data = base64_decode($data);
					$imageName=$Doctor_mobile_number . '.png';
					$file = 'profile_doc_images/'. $Doctor_mobile_number . '.png';
                    
				}else{
					$imageName = "account_image.png";
				}
			
			
			// Input validations
			if(!empty($Doctor_mobile_number) and !empty($Doctor_password) and !empty($Doctor_name) and !empty($Doctor_dob) and !empty($Doctor_email)){
			        
					// code to check for uniqueness of mobile number
					$sql_mob_unique=mysql_query("select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number'",$this->db);
					if(mysql_num_rows($sql_mob_unique)==0)
					{
					  
					    $query_test="insert into doctor_stub (Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,my_type,country_code) values('$Doctor_name','$Doctor_dob','$Doctor_email','$Doctor_specialization','$Doctor_qualification','$Doctor_HospitalName','$Doctor_Country','$Doctor_State','$Doctor_City','$Doctor_Address','$Doctor_mobile_number',md5('$Doctor_password'),'$imageName','$Doctor_yxp','$license_number','$visibility','$my_type','$country_code')";
                        
						//$this->logger->write("INFO query:",$query_test);
			        
						$sql = mysql_query("insert into doctor_stub (Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_password, Doctor_photograph,Doctor_yxp,license_number,visibility,my_type,country_code) values('$Doctor_name','$Doctor_dob','$Doctor_email','$Doctor_specialization','$Doctor_qualification','$Doctor_HospitalName','$Doctor_Country','$Doctor_State','$Doctor_City','$Doctor_Address','$Doctor_mobile_number',md5('$Doctor_password'),'$imageName','$Doctor_yxp','$license_number','$visibility','$my_type','$country_code')", $this->db);
						
					   
					   $id_doc= mysql_insert_id();
					   if($sql=='success'){
					        
							if($Doctor_photograph!=""){
							  $this->logger->write("INFO inside file image putting");
							  $file_put = file_put_contents($file, $data);
							}
							
							//to check whether there is any entry in hospital_refer_in_doctor_stub
							//checks if a doctor is already referred and the doctor is registering later, then the auto doctor id gets mapped to the hospital_refer_in_doctor_stub table and gets updated in the entry
							$query_check_refer_in=mysql_query("select * from hospital_refer_in_doctor_stub where refer_in_doc_mobile='$Doctor_mobile_number' and doc_stub_id='' ");
							
							$count_refer_in=mysql_num_rows($query_check_refer_in);
							
							if($count_refer_in>0){
								$query_update_refer_in=mysql_query("update hospital_refer_in_doctor_stub set doc_stub_id ='$id_doc' where refer_in_doc_mobile='$Doctor_mobile_number'");
								$visibility=0;// off for the referred in doctors
								$query_update_visibility=mysql_query("update doctor_stub set visibility='$visibility' where Doctor_serial_id=".$id_doc);
							}
							
							$success = array('status' => "Success", "msg" => "Successfully doctor has been registered");
							$this->response($this->json($success),200);
						 }
					  else
						 {
							$error = array('status' => "Failed", "msg" => "SQL query failed");
							$this->response($this->json($error), 400);
						 }
				
			     }
			     
			     //for handing temp doctors that is added as a referring doctor before the doctor registers
			     $this->logger->write("INFO :","before handling temp doctors");
				 $temp='temp';
				 $doc_unregister='False';
			     $sql_mob_unique_temp=mysql_query("select * from doctor_stub where Doctor_mobile_number='$Doctor_mobile_number' and Doctor_unregistered='$temp'",$this->db);
			     
			     $this->logger->write("INFO :","count handling temp doctors".mysql_num_rows($sql_mob_unique_temp));
			     
			     if(mysql_num_rows($sql_mob_unique_temp)>0)
			     {
			        $res_sql_mob=mysql_fetch_assoc($sql_mob_unique_temp);
			        $doc_ser_id=$res_sql_mob['Doctor_serial_id'];
			        
			        $update_doctor=mysql_query("update doctor_stub set Doctor_name='$Doctor_name',Doctor_dob='$Doctor_dob',Doctor_email='$Doctor_email',Doctor_specialization='$Doctor_specialization',Doctor_qualification='$Doctor_qualification',Doctor_HospitalName='$Doctor_HospitalName',Doctor_Country='$Doctor_Country',Doctor_State='$Doctor_State',Doctor_City='$Doctor_City',Doctor_Address='$Doctor_Address',Doctor_password=md5('$Doctor_password'),Doctor_photograph='$imageName',Doctor_yxp='$Doctor_yxp',license_number='$license_number',visibility='$visibility',my_type='$my_type',country_code='$country_code',Doctor_unregistered='$doc_unregister' where Doctor_mobile_number='$Doctor_mobile_number'");
			        
			        //updating hospital_refer_in_doctor_stub
			        $update_refer_in=mysql_query("update hospital_refer_in_doctor_stub set doc_stub_id='$doc_ser_id' where refer_in_doc_mobile='$Doctor_mobile_number'");
			        
			        $this->logger->write("INFO :","after handling temp doctors");
			        
			        $success = array('status' => "Success", "msg" => "Successfully doctor has been registered");
			        $this->response($this->json($success),200);
			     }
			     
				 /* else
				 {
				    $error = array('status' => "Failed", "msg" => "A user already registered with this mobile number");
					$this->response($this->json($error), 400);
				 } */
				 
			     $error = array('status' => "Failed", "msg" => "A user already registered with this mobile number");
			     $this->response($this->json($error), 400);
			
			
			}
			else
			{
			  // If invalid inputs "Bad Request" status message and reason
			  $error = array('status' => "Failed", "msg" => "Invalid inputs provided");
			  $this->response($this->json($error), 400);
			}
		}
		
	
		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new REGISTRATION_DOCTOR;
	$api->processApi();
?>