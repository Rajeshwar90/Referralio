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
			include_once("Email_sender.php");
			include_once("GCM.php");
			include_once("GCM_SALES.php");
			include_once("db_functions.php");
			include_once("iosPushUniversal.php");
			$this->logger = new Logger();
			$this->email_sender= new EMAIL_SENDER();
			$this->db_func = new DB_Functions();
			$this->gcm= new GCM();
			$this->gcm_sales= new GCM_SALES();
			$this->obj = new iosPushUniversal();
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
		    $filename="";
		    $filename_name="";
		    $post = json_decode(file_get_contents("php://input"), true);
		    $login_encrypted_key = $post['enc_key'];
		    $login_id=$post['login_id'];
		    $doc_ref_id=$post['doc_ref_id'];
		    $Doctor_audio=$post['Doctor_audio'];
		    
		    $doc_ref_email="";
		    $hospital_user_available=0;
		    $count_new_pat_client=0;
		    
		    //get doctor name
		    
		    $sql_get_doc_name = mysql_query("SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND BINARY Doctor_serial_id = '$login_id'", $this->db);
		    	
		    	
		    if(mysql_num_rows($sql_get_doc_name) > 0){
		        $result_sql = mysql_fetch_array($sql_get_doc_name,MYSQL_ASSOC);
		        $doctor_name= $result_sql['Doctor_name'];
		    }
		    
		    //get doc_ref_id doctor name
		    $doc_ref_id_name="";
		    $doc_ref_id_number="";
		    $query_get_doc_ref_id=mysql_query("select * from doctor_stub where Doctor_serial_id='$doc_ref_id'",$this->db);
		    while($row_doc_ref=mysql_fetch_assoc($query_get_doc_ref_id)){
		        $doc_ref_id_name=$row_doc_ref['Doctor_name'];
		        $doc_ref_id_number=$row_doc_ref['Doctor_mobile_number'];
		        $doc_ref_email=$row_doc_ref['Doctor_email'];
		    }
		    
		    $today_time="";
		    $date = new DateTime();
		    $today_time=$date->getTimestamp();
		    
		    $filename="transaction_audio_rec/".$today_time.'_'.$login_id.'_'.$doc_ref_id.".wav";
		    $filename_name=$today_time.'_'.$login_id.'_'.$doc_ref_id.".wav";
		    
		    $this->logger->write("INFO :","login with".$login_encrypted_key."login_id".$login_id."filename".$filename."filename_name".$filename_name);
		   
		    if($Doctor_audio!=""){
		        file_put_contents($filename, base64_decode($Doctor_audio));
		    }
			// Input validations
			if(!empty($login_id) and !empty($doc_ref_id) and !empty($Doctor_audio)){
			    
			    //to get the hospital Id
			    //checking for hospital_refer_out_doc_stub if that doctor is registered with any hospital.If any get the hospital id
			    //if not get the referralio default hospital id
			    $query_get_hospital_doc_val="select * from hospital_refer_out_doctor_stub where doctor_stub_id='$doc_ref_id'";
			     
			    $this->logger->write("INFO :","patient register".$query_get_hospital_doc_val);
			     
			    $res_get_hos_doc_val=mysql_query($query_get_hospital_doc_val,$this->db);
			     
			    $count_hos_val=mysql_num_rows($res_get_hos_doc_val);
			     
			    $this->logger->write("INFO :","count of hospital value".$count_hos_val);
			     
			    if($count_hos_val>0){
			        $row_val=mysql_fetch_assoc($res_get_hos_doc_val);
			        $hos_id_trans=$row_val['hospital_id'];
			    }else{
			        $hos_id_trans=0;
			    }
			     
			    $this->logger->write("INFO :","hos_id_trans".$hos_id_trans);
			    
			    $sql_print=mysql_query("insert into patient_stub (Patient_Name, Patient_Age, Patient_Gender, Patient_Location, Patient_mobile_number, Patient_issue_notes, Reg_by_doc, Patient_defined_notes, doc_ref_id,hospital_id_transaction,audio_val) values('Voice','','Male','Voice','Voice','$Patient_issue','$login_id','Voice','$doc_ref_id','$hos_id_trans','$filename_name')");
			    
			    
			    //changes for referral mapping
			    $hospital_user_id="";
			    $mapping_id="";
			    $patient_auto_id=mysql_insert_id();
			    //query to get the hospital for the hospital doctor
			    $query_get_hospital="select * from hospital_refer_out_doctor_stub where doctor_stub_id='$doc_ref_id'";
			    $res_get_hospital=mysql_query($query_get_hospital);
			    if(mysql_num_rows($res_get_hospital)==1)
			    {
			        $hospital_user_available=1;
			        $row_get_hospital=mysql_fetch_assoc($res_get_hospital);
			        $hospital_id=$row_get_hospital['hospital_id'];
			    
			        //query to check for any hospital_user for this referring doctor
			        $query_get_referral_map="select * from referral_mapping where hospital_id='$hospital_id' and referring_doctor_id='$login_id'";
			        $res_referral_map=mysql_query($query_get_referral_map);
			        if(mysql_num_rows($res_referral_map)==1){
			            $row_get_referral_map=mysql_fetch_assoc($res_referral_map);
			            $hospital_user_id=$row_get_referral_map['hospital_user_id'];
			            $mapping_id=$row_get_referral_map['mapping_id'];
			    
			            $referral_map_pat_entry="insert into referral_mapping_patient_stub(mapping_hospital_user_id,mapping_id,patient_stub_id) values ('$hospital_user_id','$mapping_id','$patient_auto_id')";
			            $referral_map_pat_entry=mysql_query($referral_map_pat_entry);
			    
			            //getting count of new patients that are not viewed by salesperson(sales view flag)
			            //push for count of new patient view flag
			            $query_push_count_pat=mysql_query("SELECT count(*) as cnt FROM `referral_mapping_patient_stub` rm where rm.mapping_hospital_user_id='$hospital_user_id' and rm.sales_view_flag=0 ",$this->db);
			            $row_push_cnt_view=mysql_fetch_assoc($query_push_count_pat);
			            $count_new_pat_client=$row_push_cnt_view['cnt'];
			    
			        }else{
			            //do nothing
			        }
			    }else
			    {
			        //do nothing for the time being
			    }
			    
			    //end of changes for referral map entry
			    
			    
			    $this->logger->write("INFO :","sql voice".$sql_print);
			    
			    if($sql_print="success"){
			        
			        $this->logger->write("INFO :","login doc_ref_id".$doc_ref_id);
			        
			        $sql_refer_in_cnt=mysql_query("select count(*) as cnt from patient_stub where doc_ref_id='$doc_ref_id' and refer_in_view_flag=0 and reg_by_doc='$login_id'",$this->db);
			        
			        $result_refer_in = mysql_fetch_array($sql_refer_in_cnt,MYSQL_ASSOC);
			        
			        $cnt_refer_in=$result_refer_in['cnt'];
			        $this->logger->write("INFO :","refer in count".$cnt_refer_in);
			        
			        
			        $sql_gcm=mysql_query("select mobile_os_type,gcm_regid from gcm_users gs where gs.mob_number=(select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$doc_ref_id')",$this->db);
			        
			        $this->logger->write("INFO :","mysql_num_rows inside sql_gcm".mysql_num_rows($sql_gcm));
			        	
			        $num=mysql_num_rows($sql_gcm);
			        	
			        $this->logger->write("INFO :","num inside num".$num);
			        
			        $mobile_os="";
			        if($num > 0){
			            $result_sql_gcm = mysql_fetch_array($sql_gcm,MYSQL_ASSOC);
			            $gcm_id=$result_sql_gcm['gcm_regid'];
			            $mobile_os=$result_sql_gcm['mobile_os_type'];
			            $this->logger->write("INFO :","login gcm_id".$gcm_id."os type=>".$mobile_os);
			            
			            //for sending text message to referred doctor
			            $msg_doc_txt="A Patient with voice message has been referred to you by Dr.".$doctor_name;
			            $this->send_msg($doc_ref_id_number,$msg_doc_txt);
			            
			            //for sending emails
			            $email_msg="A Patient with voice message has been referred to you by Dr.".$doctor_name;
			            $this->email_sender->send_email($doc_ref_email,$email_msg);
			            
			            if($hospital_user_available==1){
			                //sending push to the hospital user if any
			                //need to get the gcm id of the hospital user
			                $sql_gcm_hospital_user=mysql_query("select mobile_os_type,gcm_regid from gcm_users gs where gs.mob_number=(select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$hospital_user_id')",$this->db);
			                	
			                $this->logger->write("INFO :","mysql_num_rows inside sql_gcm_hospital_user".mysql_num_rows($sql_gcm_hospital_user));
			                
			                if(mysql_num_rows($sql_gcm_hospital_user)>0){


			                    $result_sql_gcm_hospital_user= mysql_fetch_array($sql_gcm_hospital_user,MYSQL_ASSOC);
			                    $gcm_id_hospital_user=$result_sql_gcm_hospital_user['gcm_regid'];
			                    $mobile_os_hospital_user=$result_sql_gcm_hospital_user['mobile_os_type'];
			                    $this->logger->write("INFO :","login gcm_id".$gcm_id_hospital_user."=>OS=>".$mobile_os_hospital_user);
			                    	
			                    if($mobile_os_hospital_user == 'Android'){
			                        $this->logger->write("INFO :","inside android sales".$gcm_id_hospital_user);
			                        $registatoin_ids = array($gcm_id_hospital_user);
			                        $message = array("msg" => "DR.".$doctor_name." have reffered a voice patient to Dr.".$doc_ref_id_name ,"flag_push"=> "Refer", "Refer_In_cnt" => $count_new_pat_client);
			                        $this->logger->write("INFO :","Message*****".$message);
			                        $result1 = $this->gcm_sales->send_notification($registatoin_ids, $message);
			                        $this->logger->write("INFO :","result1*****".$result1);
			                    }else if($mobile_os_hospital_user == 'IOS'){
			                        $this->logger->write("INFO :","inside IOS".$gcm_id_hospital_user);
			                        $registatoin_ids = array($gcm_id_hospital_user);
			                        $message ="DR.".$doctor_name." have reffered a voice patient to Dr.".$doc_ref_id_name;
			                        $extra = array("msg" => "DR.".$doctor_name." have reffered a voice patient to Dr.".$doc_ref_id_name ,"flag_push"=> "Refer", "Refer_In_cnt" => $count_new_pat_client);
			                        $result = $this->obj->sendIosPush($registatoin_ids,$message,$extra);
			                        $this->logger->write("INFO :","inside IOS result".$result);
			                        /* $this->logger->write("INFO :","inside IOS".$gcm_id_hospital_user);
			                         $registatoin_ids = array($gcm_id_hospital_user);
			                         $message = array("msg" => "DR.".$doctor_name." have reffered a patient ".$Patient_Name."to Dr.".$doc_ref_id_name ,"flag_push"=> "Refer", "Refer_In_cnt" => $count_new_pat_client);
			                        $result=$this->obj->sendIosPush($registatoin_ids,$message); */
			                    }
			                    	
			                    	
			                }
			            }
			            
			            if($mobile_os == 'Android'){
			                $this->logger->write("INFO :","inside android".$gcm_id);
			                $registatoin_ids = array($gcm_id);
			                $message = array("msg" => "DR.".$doctor_name." has reffered a voice patient","flag_push"=> "Refer", "Refer_In_cnt" => $cnt_refer_in);
			                $result = $this->gcm->send_notification($registatoin_ids, $message);
			                $this->logger->write("INFO :","result+++++".$result);
			            }else if($mobile_os == 'IOS'){
			                $this->logger->write("INFO :","inside IOS".$gcm_id);
			                /* $registatoin_ids = array(
			                 '68141f71cbae1c493318f17b132215223655c27a2670a580dee074c4cdf96028'
			                ); */
			                $registatoin_ids = array($gcm_id);
			                /* $message = array(
			                 "alert" => "DR.".$doctor_name." have reffered a patient ".$Patient_Name
			                  
			                ); */
			                	
			                $message ="DR.".$doctor_name." have reffered a voice patient";
			                	
			                $extra =array("msg" => "DR.".$doctor_name." has reffered a voice patient ","flag_push"=> "Refer", "Refer_In_cnt" => $cnt_refer_in);
			                	
			                //$message=array("check"=>"hello123");
			                //$message="Hello456gr";
			                $result = $this->obj->sendIosPush($registatoin_ids,$message,$extra);
			                $this->logger->write("INFO :","inside IOS result".$result);
			            }

			            $success = array('status' => "Success", "msg" => "Referred successfully");
			            $this->response($this->json($success), 200);
			        }
			       
			    }else{
			        $success = array('status' => "Failed", "msg" => "Could not refer");
			        $this->response($this->json($success), 200);
			    }
			    
			    
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
		
		private function send_msg($mobile,$mobile_msg){
		
		    $this->logger->write("INFO :","login with mobile".$mobile);
		    //$ch = curl_init();
		    //curl_setopt($ch,CURLOPT_VERBOSE,1);
		    $msg="";
		    	
		    $msg=urlencode($mobile_msg);
		    	
		    	
		    $this->logger->write("INFO :","login with msg".$msg);
		    $url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;
		    	
		    $this->logger->write("INFO :","login with url".$url);
		
		    $ch = curl_init();  // setup a curl
		    curl_setopt($ch, CURLOPT_URL, $url);  // set url to send to
		    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    $output=curl_exec($ch);
		    //echo "output".$output;
		    curl_close($ch);
		    return $output;
		}
	}
	
	// Initiiate Library
	
	$api = new ONESHOTFILEUPLOAD;
	$api->processApi();
?>