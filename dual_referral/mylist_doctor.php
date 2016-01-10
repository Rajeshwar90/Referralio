<?php
    
	/* Developed By Rajeshwar Bose
	   PHP: 5.4.37 Date: 05-26-2015*/
		
	require_once("Rest.inc.php");
	
	class MYLIST_DOCTOR extends REST {
	
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
		
		private function LISTING_MYDOC(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling LISTING_MYDOC for doctor");
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
						 
						 $query_list_doc=mysql_query("select  temp.*,temp.personal_list_doc_id,count(case when pt.read_flag='unread' then 1 end) as cnt from (SELECT * FROM doctor_personal_list pl INNER JOIN doctor_stub ds ON pl.personal_list_doc_id=ds.Doctor_serial_id where pl.doc_id='$login_id' and ds.Doctor_unregistered='False' order by TImestamp DESC)as temp inner join pat_thread_msg pt on pt.login_id=temp.personal_list_doc_id group by temp.personal_list_doc_id",$this->db);
						 
						 if(mysql_num_rows($query_list_doc)>0){
						   while($row = mysql_fetch_assoc($query_list_doc)){
									$json[] = $row;
								}
							$success = array('status' => "Success", "msg" => "Mylist Doctors available","Doc_list" => $json);
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
	
	$api = new MYLIST_DOCTOR;
	$api->processApi();
?>