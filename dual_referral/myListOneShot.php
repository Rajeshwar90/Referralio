<?php
    
	/* Developed By Rajeshwar Bose
	   PHP: 5.4.37 Date: 05-26-2015*/
		
	require_once("Rest.inc.php");
	
	class MYLISTONESHOT extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "refSqlRef007";
		const DB = "referralapp";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
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
		 *	Simple listing API
		 *  POST method
		 *  enc_key : <Encrypted KEY>
		 *  login_id : <User id autoincremented>
		 */
		
		private function LISTING_ONESHOT(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling MYLISTONESHOT for doctor");
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			$login_encrypted_key = $post['enc_key'];
			$login_id=$post['login_id'];
			
				
			$this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id);
			
			
			//$mob_number = $this->_request['Doctor_mobile_number'];		
			//$password = $this->_request['pwd'];
			
			// Input validations
			if(!empty($login_encrypted_key) and !empty($login_id)){
					$sql = mysql_query("SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id'", $this->db);
					if(mysql_num_rows($sql) > 0){
					     
						 //select  temp.*,temp.personal_list_doc_id,count(case when pt.read_flag='unread' then 1 end) as cnt from (SELECT * FROM doctor_personal_list pl INNER JOIN doctor_stub ds ON pl.personal_list_doc_id=ds.Doctor_serial_id where pl.doc_id='42' and ds.Doctor_unregistered='False' order by TImestamp DESC)as temp inner join pat_thread_msg pt on pt.login_id=temp.personal_list_doc_id group by temp.personal_list_doc_id
						 
						 //SELECT * FROM doctor_personal_list pl INNER JOIN doctor_stub ds ON pl.personal_list_doc_id=ds.Doctor_serial_id where pl.doc_id='$login_id' and ds.Doctor_unregistered='False' order by TImestamp DESC
						 
						 /* $query_list_doc=mysql_query("SELECT temp . Doctor_serial_id,temp.Doctor_name,temp.Doctor_dob,temp.Doctor_email,temp.Doctor_specialization, temp.Doctor_qualification, temp.Doctor_HospitalName,temp.Doctor_Country,temp.Doctor_State,temp.Doctor_City,temp.Doctor_Address, temp.Doctor_mobile_number, temp.Doctor_photograph,temp.Doctor_yxp,temp.type_value FROM(SELECT *
FROM doctor_personal_list pl
INNER JOIN doctor_stub ds ON pl.personal_list_doc_id = ds.Doctor_serial_id
WHERE pl.doc_id =  '$login_id'
AND ds.Doctor_unregistered =  'False'
ORDER BY TIMESTAMP DESC)AS temp
INNER JOIN pat_thread_msg pt ON pt.login_id = temp.personal_list_doc_id
GROUP BY temp.personal_list_doc_id UNION select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 and type_value='' order by Doctor_name ASC",$this->db); */
					    
					    //$query_get_mylist=""
						
					    $query_list_doc = mysql_query ( "select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' order by Doctor_name ASC", $this->db );
					    
						 if(mysql_num_rows($query_list_doc)>0){
						    
						     //query to check if this doctor is referred in
						     $referred_str="";
						     $query_get_referred_in="select refer_by_hos_id from hospital_refer_in_doctor_stub where doc_stub_id='$login_id'";
						     $result_get_referred_in_hos=mysql_query ($query_get_referred_in);
						     
						     if(mysql_num_rows($result_get_referred_in_hos)>0){
						         $referred_in=1;
						         while($row_hos=mysql_fetch_assoc($result_get_referred_in_hos)){
						             $this->logger->write("Element===>".$row_hos['refer_by_hos_id']);
        				             $referred_str=$referred_str.",".$row_hos['refer_by_hos_id'];
        				             $this->logger->write("refer str".$referred_str);
        				             $referred_str=trim($referred_str,",");
        				             $this->logger->write("refer str after trim".$referred_str);
						         }
						     }else{
						         $referred_in=0;
						     }
						     
						     
						     //query_to_check_hospital doctor
						      
						     $query_get_hospital_doc=mysql_query("select hospital_id from hospital_refer_out_doctor_stub where doctor_stub_id='$login_id'");
						     if(mysql_num_rows($query_get_hospital_doc)>0){
						         $hospital_in=1;
						     }else{
						         $hospital_in=0;
						     }
						     
						     if($referred_in==1){
						         $query_list = "select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub ds inner join (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id IN ($referred_str)) as res1 on res1.doctor_stub_id=ds.Doctor_serial_id where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 order by Doctor_name ASC";
						     
						         //select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub where type_value='hospital' and Doctor_yxp in (5,9,11)
						     
						         //select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub ds inner join (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id IN (',9')) as res1 on res1.doctor_stub_id=ds.Doctor_serial_id where Doctor_serial_id!='2' and Doctor_unregistered='False' and visibility=1 order by Doctor_name ASC
						     
						         $query_list_doc=mysql_query($query_list,$this->db);
						          
						     }else  if($hospital_in==1){
						         $query_list ="select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub ds inner join (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id =(select hospital_id from hospital_refer_out_doctor_stub where doctor_stub_id='$login_id') and doctor_stub_id!='$login_id') as res1 on res1.doctor_stub_id=ds.Doctor_serial_id where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 order by Doctor_name ASC";
						          
						         $query_list_doc=mysql_query($query_list,$this->db);
						     } else if($referred_in==1 && $hospital_in==1){
						         $query_list="select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub ds inner join (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id IN ($referred_str)) as res1 on res1.doctor_stub_id=ds.Doctor_serial_id where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 UNION select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub ds inner join (select doctor_stub_id from hospital_refer_out_doctor_stub where hospital_id =(select hospital_id from hospital_refer_out_doctor_stub where doctor_stub_id='$login_id') and doctor_stub_id!='$login_id') as res1 on res1.doctor_stub_id=ds.Doctor_serial_id where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 order by Doctor_name ASC";
						         $query_list_doc=mysql_query($query_list,$this->db);
						     }else{
						         $query_list = "select Doctor_serial_id,Doctor_name, Doctor_dob, Doctor_email, Doctor_specialization, Doctor_qualification, Doctor_HospitalName,Doctor_Country,Doctor_State,Doctor_City,Doctor_Address, Doctor_mobile_number, Doctor_photograph,Doctor_yxp,type_value from doctor_stub where Doctor_serial_id!='$login_id' and Doctor_unregistered='False' and visibility=1 order by Doctor_name ASC";
						          
						         $query_list_doc=mysql_query($query_list,$this->db);
						     }
						     
						     $this->logger->write("INFO :","listing doctor query_list_doc".$query_list." referred_str=> ".$referred_str);
						     
						   while($row = mysql_fetch_assoc($query_list_doc)){
									$json[] = $row;
								}
							$success = array('status' => "Success","msg" => "Mylist Doctors available","Doc_list" => $json);
					        $this->response($this->json($success), 200);
							
						 }
						 else
						 {
						    $success = array('status' => "Success", "msg" => "No doctors available");
					        $this->response($this->json($success), 204);
						 }
					}
					else
					{
					  $error1 = array('status' => "Failed", "msg" => "Basic authentication failed");
					  $this->response($this->json($error1), 203);	// If no records "No Content" status
                    }					  
			}
			else{
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Either encrypted key or id is empty");
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
	
	$api = new MYLISTONESHOT;
	$api->processApi();
?>