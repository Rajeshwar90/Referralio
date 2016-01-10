<?php
    
	/* Developed By Rajeshwar Bose
	   PHP: 5.4.37 Date: 05-26-2015*/
		
	require_once("Rest.inc.php");
	require_once 'parameters.php';
	class PATIENT_MSG_API extends REST {
	    public $data = "";
	    // private $db;
	    private $username;
	    private $password;
	    private $server;
	    private $db = NULL;
	    const DB_SERVER = "localhost";
	    const DB_USER = "root";
	    const DB_PASSWORD = "refSqlRef007";
	    const DB = "referralapp";
	    public function __construct() {
	        parent::__construct (); // Init parent contructor
	        $this->dbConnect (); // Initiate Database connection
	        include_once ("logger.php");
	        $this->logger = new Logger ();
	        $this->logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
	        $this->logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );
	        /* $this->server = DB_SERVER;
	        $this->username = DB_USER;
	        $this->password = DB_PASSWORD;
	        $this->db = DB; */
	    }
		
		/*
     * Database connection
     */
    private function dbConnect() {
        /* $this->conn = mysql_connect ( $this->server, $this->username, $this->password );
        if ($this->conn)
            mysql_select_db ( $this->db, $this->conn ); */
        
        $this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
        if($this->db)
            mysql_select_db(self::DB,$this->db);
    }
    
    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi() {
        $func = strtolower ( trim ( str_replace ( "/", "", $_REQUEST ['request'] ) ) );
        if (( int ) method_exists ( $this, $func ) > 0)
            $this->$func ();
        else
            $this->response ( '', 404 ); // If the method not exist with in this class, response would be "Page not found".
    }
		
		/* 
		 *	Simple listing API
		 *  POST method
		 *  enc_key : <Encrypted KEY>
		 *  login_id : <User id autoincremented>
		 */
		
		private function MSG_API(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling MSG_API for sales");
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			$login_encrypted_key = $post['enc_key'];
			$login_id=$post['login_id'];
			//$doc_id=$post['doc_id'];
			$pat_thrd_id=$post['pat_thrd_id'];
			$message=$post['msg'];
			$message=mysql_real_escape_string($message);
			$action=$post['action'];
			$read_flag='read';
			$unread_flag='unread';
			
			$original_doc_id="";
			
				
			$this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id."pat_thrd_id".$pat_thrd_id."doc_idtest".$login_id);
			
			
			//$mob_number = $this->_request['Doctor_mobile_number'];		
			//$password = $this->_request['pwd'];
			
			// Input validations
			if(!empty($login_encrypted_key) and !empty($login_id)){
					$sql = mysql_query("SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id'", $this->db);
					if(mysql_num_rows($sql) > 0){
					
					     if($action=='' || $action=='msgreq'){
						     
						   if($message!=""){
						    
							$query_insert_msg=mysql_query("INSERT INTO pat_thread_msg(pat_thrd_id, login_id,doc_id,message,read_flag) VALUES ('$pat_thrd_id','$login_id','$login_id','$message','$unread_flag')",$this->db);
							
							//checking and updating the primary flag if the doctor who was referred to messages back
							$res_query_update_primary_flag_UI=mysql_query("select * from patient_stub where Patient_thread_id='$pat_thrd_id'");
							
							while($row_pat_id=mysql_fetch_assoc($res_query_update_primary_flag_UI)){
								$original_doc_id=$row_pat_id['doc_ref_id'];
							}
							$primary_status=1;
							if($original_doc_id==$login_id){
								$res_update_primary_flag=mysql_query("update patient_stub set primary_status='$primary_status' where Patient_thread_id='$pat_thrd_id'");
							}
							
							//end of checking and updating the primary flag
							
							
							if($query_insert_msg=='success')
						    {
						      $this->logger->write("INFO :","doc_id doc_id".$login_id);
							  //$success = array('status' => "Success", "msg" => "Message sent");
							  
							   /*Getting required fields for PUSH*/
							   
							   $get_pat_sql=mysql_query("select * from patient_stub where Patient_thread_id='$pat_thrd_id'",$this->db);
							   
							   $result_get_pat_name = mysql_fetch_array($get_pat_sql,MYSQL_ASSOC);
							   $pat_name_fetch=$result_get_pat_name['Patient_Name'];
							   $pat_refer_date=$result_get_pat_name['TImestamp'];
							   $pat_gender=$result_get_pat_name['Patient_Gender'];
							   $pat_age=$result_get_pat_name['Patient_Age'];
							   $pat_loc=$result_get_pat_name['Patient_Location'];
							   $pat_mobile=$result_get_pat_name['Patient_mobile_number'];
							   $pat_note=$result_get_pat_name['Patient_defined_notes'];
							   $pat_issue_note=$result_get_pat_name['Patient_issue_notes'];
							   
							  
							   /*end of getting fields for push*/
							   
							   
							   //code to push to the referring doctor and the hospital doctor
							   /*$query_get_reg_ref="select gs.gcm_regid,gs.mobile_os_type,temp2.* from gcm_users gs inner join (select ds.doctor_mobile_number from doctor_stub ds inner join (SELECT Reg_by_doc,doc_ref_id FROM `patient_stub` where Patient_thread_id='$pat_thrd_id')as temp1 on temp1.reg_by_doc=ds.Doctor_serial_id or temp1.doc_ref_id=ds.Doctor_serial_id)as temp2 on temp2.doctor_mobile_number=gs.mob_number";
							   
							   $res_get_reg_ref=mysql_query($query_get_reg_ref,$this->db);
							   while($row_get_reg_ref=mysql_fetch_assoc($res_get_reg_ref)){
							       $doc_gcm=$row_get_reg_ref['gcm_regid'];
							       $doc_mobile_os=$row_get_reg_ref['mobile_os_type'];
							       
							       if($doc_mobile_os == 'Android'){
							           $this->logger->write("INFO :","inside android".$doc_gcm);
							           $registatoin_ids = array($gcm_id);
							           $message = array("msg" => "DR.".$doctor_name." have reffered a patient ".$Patient_Name,"flag_push"=> "Refer", "Refer_In_cnt" => $cnt_refer_in);
							           $result = $this->gcm->send_notification($registatoin_ids, $message);
							       }else if($doc_mobile_os == 'IOS'){
							           $this->logger->write("INFO :","inside IOS".$doc_gcm);
							           
							           $registatoin_ids = array($gcm_id);
							           $message ="DR.".$doctor_name." have reffered a patient ".$Patient_Name;
							           	
							           $extra =array("msg" => "DR.".$doctor_name." have reffered a patient ".$Patient_Name,"flag_push"=> "Refer", "Refer_In_cnt" => $cnt_refer_in);
							           $result = $this->obj->sendIosPush($registatoin_ids,$message,$extra);
							           $this->logger->write("INFO :","inside IOS result".$result);
							       }
							       
							   }*/
							   
							   
							   
							   //end of code to the push for referring doctor and the hospital doctor
							   
							  
							  /*code for push notification to doc_id for message*/
							  
							  //count of unread msg
							  
							   //$count_sql="select count(*) as cnt from (select * from patient_stub ps where ps.doc_ref_id='$login_id' and ps.reg_by_doc='$doc_id') as temp inner join pat_thread_msg pmsg on temp.patient_thread_id=pmsg.pat_thrd_id where pmsg.login_id='$login_id' and pmsg.doc_id='$doc_id' and pmsg.read_flag='unread'";
							  
							  //$count_msg=mysql_query("select count(*) as cnt from (select * from patient_stub ps where ps.doc_ref_id='$login_id' and ps.reg_by_doc='$doc_id') as temp inner join pat_thread_msg pmsg on temp.patient_thread_id=pmsg.pat_thrd_id where pmsg.login_id='$login_id' and pmsg.doc_id='$doc_id' and pmsg.read_flag='unread'");
							  
							  //$result_cnt = mysql_fetch_array($count_msg,MYSQL_ASSOC);
							  //$cnt=$result_cnt['cnt'];
							  
							  //$this->logger->write("INFO :","count_msg count_msg".$count_sql);
							  //$this->logger->write("INFO :","msg cnt".$cnt);
							  
							  $get_doc_name=mysql_query("select Doctor_name from doctor_stub where Doctor_serial_id='$login_id'",$this->db);
							  
							  $result_get_doc_name = mysql_fetch_array($get_doc_name,MYSQL_ASSOC);
							  $doc_name_fetch=$result_get_doc_name['Doctor_name'];
							  
							  
							  $sql_gcm=mysql_query("select gcm_regid from gcm_users gs where gs.mob_number=(select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$doc_id')",$this->db);
								
								 //$this->logger->write("INFO :","mysql_num_rows inside sql_gcm".mysql_num_rows($sql_gcm));
								 
								 //$num=mysql_num_rows($sql_gcm);
								 
								 //$this->logger->write("INFO :","num inside num".$num);
								 
								 /* if($num > 0){
									$result_sql_gcm = mysql_fetch_array($sql_gcm,MYSQL_ASSOC);
									$gcm_id=$result_sql_gcm['gcm_regid'];
									$this->logger->write("INFO :","doc gcm_id".$gcm_id);
									
									$registatoin_ids = array($gcm_id);
									
									$req_val=array("patient_name" => $pat_name_fetch,"REFER_DATE" => $pat_refer_date,"doctor_name" => $doc_name_fetch,"doctor_id"=> $login_id,"pat_thread_id" => $pat_thrd_id,"pat_gender"=>$pat_gender,"pat_age"=>$pat_age,"pat_loc"=>$pat_loc,"pat_mobile"=>$pat_mobile,"pat_note"=>$pat_note,"pat_issue_note"=>$pat_issue_note,"cnt"=>$cnt);
									
									$message = array("msg" => $this->json($req_val),"flag_push"=>"Message");
									$result = $this->gcm->send_notification($registatoin_ids, $message); 
							  } */
							  
							  /*end of code for push notification to doc_id*/
							  
							  $result="";//added as this time result is going as null
							  $query_print="SELECT pmsg.message,pmsg.doc_id,ds.Doctor_name,pmsg.timestamp  FROM pat_thread_msg pmsg INNER JOIN doctor_stub ds ON pmsg.login_id=ds.Doctor_serial_id where pmsg.pat_thrd_id='$pat_thrd_id' order by TImestamp ASC";
							  
							  //$this->logger->write("INFO :","query_print query_print".$query_print);
							  
							  $query_msg=mysql_query("SELECT pmsg.message,pmsg.doc_id,ds.Doctor_name,pmsg.timestamp  FROM pat_thread_msg pmsg INNER JOIN doctor_stub ds ON pmsg.login_id=ds.Doctor_serial_id where pmsg.pat_thrd_id='$pat_thrd_id' order by TImestamp ASC",$this->db);
							  
							  $query_update_read=mysql_query("update pat_thread_msg set read_flag='$read_flag' where doc_id='$login_id' and pat_thrd_id='$pat_thrd_id' and read_flag='$unread_flag'",$this->db);
							  
							  if($query_update_read=='success')
						      {
							  }
							      if(mysql_num_rows($query_msg)>0){
								  while($row = mysql_fetch_assoc($query_msg)){
										$json[] = $row;
									}
										$success = array('status' => "Success", "msg" => "Message is present","msg_list" => $json,"PUSHresult" => $result);
										$this->response($this->json($success), 200);
										
									  }
							  
							  
								  
				
						     }else{
							    
								$error = array('status' => "Failure", "msg" => "Message could not be sent.Please try again later.");
					            $this->response($this->json($error), 400);
							 }
							
						 
						 }else{
						 
						    $query_print1="SELECT pmsg.message,pmsg.doc_id,ds.Doctor_name,pmsg.timestamp FROM pat_thread_msg pmsg INNER JOIN doctor_stub ds ON pmsg.login_id=ds.Doctor_serial_id where pmsg.pat_thrd_id='$pat_thrd_id' order by TImestamp ASC";
							  
							//$this->logger->write("INFO :","query_print1 query_print".$query_print1);
						 
						    $query_msg=mysql_query($query_print1,$this->db);
							
						    //commenting below line indicates that even if the sales person visits the page it should not get marked as read
							//$query_update_read=mysql_query("update pat_thread_msg set read_flag='$read_flag' where doc_id='$login_id' and pat_thrd_id='$pat_thrd_id' and read_flag='$unread_flag'",$this->db);
							  
							  /* if($query_update_read=='success')
						      {
							  } */
								   if(mysql_num_rows($query_msg)>0){
								   while($row = mysql_fetch_assoc($query_msg)){
											$json[] = $row;
										}
									$error = array('status' => "Failure", "msg" => "Message is empty","msg_list" => $json);
									$this->response($this->json($error), 200);
									
								  }else{
									 
									 $error = array('status' => "Failure", "msg" => "Message is empty and new conversation");
									 $this->response($this->json($error), 200);
									 
								  }
                             								  
						    
						   
						 }
							 
					}else
					 {
						 
						$query_delete_pat_thrd=mysql_query("delete from pat_thread_msg where pat_thrd_id='$pat_thrd_id'",$this->db);
							
							if($query_delete_pat_thrd=='success')
						    {
								  $success = array('status' => "Success", "msg" => "This Patient related messages got deleted.Note, the other doctor wont be able to view the messages you delete");
								  $this->response($this->json($success), 200);
							  
							 }else{
							    
								  $error = array('status' => "Failure", "msg" => "Patient messages could not be deleted");
					              $this->response($this->json($error), 200);  
							   
							 } 
						 
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
	
	$api = new PATIENT_MSG_API;
	$api->processApi();
?>