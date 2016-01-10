<?php
   	
	require_once("Rest.inc.php");
	
	class REFER_RULES extends REST {
	
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
		 *	Simple api to find which patients have been referred
		 */
		
		private function REFFERING_IN_STATS(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling REFFERING_IN for doctor"); 
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			$login_encrypted_key = $post['enc_key'];
			$login_id=$post['login_id'];
			
			$this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id);
			
			// Input validations
			if(!empty($login_encrypted_key) and !empty($login_id)){
					$sql = mysql_query("SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND BINARY Doctor_serial_id = '$login_id'", $this->db);
					if(mysql_num_rows($sql) > 0){
					     
						 //SELECT *,() FROM patient_stub ps INNER JOIN doctor_stub ds ON ps.Reg_by_doc=ds.Doctor_serial_id where ps.doc_ref_id='$login_id' and doc_ref_id !=0 order by TImestamp DESC
						 
						 $query_list_referin=mysql_query("Select tmp2.*,DS.* from (select ps.*,ifnull(tmp.cnt,0) as cnt
from (select count(1) as cnt,pat_thrd_id from pat_thread_msg where read_flag='unread' and doc_id='$login_id' group by pat_thrd_id) as tmp right join (select * from patient_stub where doc_ref_id='$login_id') ps
ON ps.patient_thread_id=tmp.pat_thrd_id order by ps.Timestamp DESC) as tmp2 join doctor_stub as DS on tmp2.reg_by_doc=DS.Doctor_serial_id",$this->db);
						 
						 
						 $refer_in_view_upd=mysql_query("update patient_stub set refer_in_view_flag=1 where doc_ref_id='$login_id'",$this->db);
						 
						 
						 if(mysql_num_rows($query_list_referin)>0){
						   while($row = mysql_fetch_assoc($query_list_referin)){
									$json[] = $row;
								}
                         
                          /*$query_cnt=mysql_query("SELECT pat_thrd_id,login_id,count( login_id) as cnt FROM pat_thread_msg where doc_id='$login_id' and read_flag='unread' group by pat_thrd_id",$this->db);

                          if(mysql_num_rows($query_cnt)>0){
						   while($row_cnt = mysql_fetch_assoc($query_cnt)){
									$json_cnt[] = $row_cnt;
								}
                         						  
						   }*/
						   
							$success = array('status' => "Success", "msg" => "Refer IN Patients available","ReferIN_list" => $json,"cnt_list" => $json_cnt);
					        $this->response($this->json($success), 200);
							
						 }
						 else
						 {
						    $success = array('status' => "Success", "msg" => "No Refer IN patients available");
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
		 *	Simple api to find which patients have been referred
		 */	    
		
		private function REFFERING_OUT_STATS(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling REFFERING_OUT for doctor"); 
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			$login_encrypted_key = $post['enc_key'];
			$login_id=$post['login_id'];
			$doc_id=$post['doc_id'];
			
			$this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id."doc_id".$doc_id);
			
			// Input validations
			if(!empty($login_encrypted_key) and !empty($login_id)){
					$sql = mysql_query("SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND BINARY Doctor_serial_id = '$login_id'", $this->db);
					if(mysql_num_rows($sql) > 0){
					     
						 //SELECT * FROM patient_stub ps INNER JOIN doctor_stub ds ON ps.doc_ref_id=ds.Doctor_serial_id where ps.Reg_by_doc='$login_id' and doc_ref_id !=0 order by TImestamp DESC
						 
						 if($doc_id=='')
						 {
						   $doc_id=0;
						 }
						 
						 $query_list_referout=mysql_query("select ps.*,ifnull(tmp.cnt,0) as cnt
from (select count(1) as cnt,pat_thrd_id from pat_thread_msg where read_flag='unread' and doc_id='$login_id' group by pat_thrd_id) as tmp right join (select * from patient_stub where $doc_id!=0 AND doc_ref_id='$doc_id' AND Reg_by_doc='$login_id') ps
ON ps.patient_thread_id=tmp.pat_thrd_id order by ps.Timestamp DESC",$this->db);
						 if(mysql_num_rows($query_list_referout)>0){
						   while($row = mysql_fetch_assoc($query_list_referout)){
									$json[] = $row;
								}
						 
						 
						 /*$query_cnt=mysql_query("SELECT pat_thrd_id,login_id,count( login_id) as cnt FROM pat_thread_msg where doc_id='$login_id' and read_flag='unread' group by pat_thrd_id",$this->db);
                          
                          if(mysql_num_rows($query_cnt)>0){
						   while($row_cnt = mysql_fetch_assoc($query_cnt)){
									$json_cnt[] = $row_cnt;
								}		
								}*/
								
							$success = array('status' => "Success", "msg" => "Refer OUT Patients available","ReferOUT_list" => $json);
					        $this->response($this->json($success), 200);
							
						 }
						 else
						 {
						    $success = array('status' => "Success", "msg" => "No Refer OUT patients available");
					        $this->response($this->json($success), 204);
						 }
					}
					else
					{
					  $error1 = array('status' => "Failed", "msg" => "Basic authentication failed");
					  $this->response($this->json($error1), 203);	// If no records "No Content" status
                    }					  
			}
			else
			{
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
	
	$api = new REFER_RULES;
	$api->processApi();
?>