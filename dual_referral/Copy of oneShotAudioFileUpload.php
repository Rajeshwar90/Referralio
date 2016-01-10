<?php
   	
	require_once("Rest.inc.php");
	
	class ONESHOTFILEUPLOAD extends REST {
	
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
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER MOBILE NUMBER>
		 *  pwd : <USER PASSWORD>
		 */
		
		private function AUDIO_FILE_UPLOAD_PAT_REFER(){
		    
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling AUDIO_FILE_UPLOAD_PAT_REFER for doctor");
		    // Cross validation if the request method is POST else it will return "Not Acceptable" status
		    if($this->get_request_method() != "POST"){
		        $this->response('',406);
		    }
		    
		    //$audio_path="/transaction_audio_rec/";
		    
		    $post = json_decode(file_get_contents("php://input"), true);
		    $login_encrypted_key = $post['enc_key'];
		    $login_id=$post['login_id'];
		    $doc_ref_id=$post['doc_ref_id'];
		    $Doctor_audio=$post['Doctor_audio'];
		    
		    
		    $date = new DateTime();
		    $today_time=$date->getTimestamp();
		    
		    $filename="transaction_audio_rec/".$today_time.'_'.$login_id.'_'.$doc_ref_id.".wav";
		    $filename_name=$today_time.'_'.$login_id.'_'.$doc_ref_id.".wav";
		    
		    $this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id."filename".$filename);
		   
		    if($Doctor_audio!=""){
		        file_put_contents($filename, base64_decode($Doctor_audio));
		    }
			// Input validations
			if(!empty($login_id) and !empty($doc_ref_id) and !empty($Doctor_audio)){
			    
			    $sql_print=mysql_query("insert into patient_stub (Patient_Name, Patient_Age, Patient_Gender, Patient_Location, Patient_mobile_number, Patient_issue_notes, Reg_by_doc, Patient_defined_notes, doc_ref_id,audio_val) values('Voice','','Male','Voice','Voice','$Patient_issue','$login_id','Voice','$doc_ref_id','$filename_name')");
			    
			    $this->logger->write("INFO :","sql voice".$sql_print);
			    
			    $success = array('status' => "Success", "msg" => "Referred successfully");
			    $this->response($this->json($success), 200);
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
	
	$api = new ONESHOTFILEUPLOAD;
	$api->processApi();
?>