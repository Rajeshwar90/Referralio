<?php
    
	/* Developed By Rajeshwar Bose
	   PHP: 5.4.37 Date: 05-26-2015*/
		
	require_once("Rest.inc.php");
	
	class LOGIN_DOCTOR extends REST {
	
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
		
		private function LOGIN_DOC(){
		    error_reporting(0);
		    $this->logger->write("INFO :","Calling LOGIN_DOC for doctor");
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$post = json_decode(file_get_contents("php://input"), true);
			$mob_number = $post['Doctor_mobile_number'];
			$password=$post['pwd'];
			
				
			$this->logger->write("INFO :","login with".$mob_number."pass".$password);
			
			
			//$mob_number = $this->_request['Doctor_mobile_number'];		
			//$password = $this->_request['pwd'];
			
			// Input validations
			if(!empty($mob_number) and !empty($password)){
					$sql = mysql_query("SELECT * FROM doctor_stub WHERE Doctor_mobile_number = '$mob_number' AND BINARY Doctor_password = md5('$password') and Doctor_unregistered='False' LIMIT 1", $this->db);
					if(mysql_num_rows($sql) > 0){
						$result = mysql_fetch_array($sql,MYSQL_ASSOC);
						// If success everythig is good send header as "OK" and user details
						function assign_rand_value($num)
							{
							// accepts 1 - 36
							  switch($num)
							  {
								case "1":
								 $rand_value = "a";
								break;
								case "2":
								 $rand_value = "b";
								break;
								case "3":
								 $rand_value = "c";
								break;
								case "4":
								 $rand_value = "d";
								break;
								case "5":
								 $rand_value = "e";
								break;
								case "6":
								 $rand_value = "f";
								break;
								case "7":
								 $rand_value = "g";
								break;
								case "8":
								 $rand_value = "h";
								break;
								case "9":
								 $rand_value = "i";
								break;
								case "10":
								 $rand_value = "j";
								break;
								case "11":
								 $rand_value = "k";
								break;
								case "12":
								 $rand_value = "l";
								break;
								case "13":
								 $rand_value = "m";
								break;
								case "14":
								 $rand_value = "n";
								break;
								case "15":
								 $rand_value = "o";
								break;
								case "16":
								 $rand_value = "p";
								break;
								case "17":
								 $rand_value = "q";
								break;
								case "18":
								 $rand_value = "r";
								break;
								case "19":
								 $rand_value = "s";
								break;
								case "20":
								 $rand_value = "t";
								break;
								case "21":
								 $rand_value = "u";
								break;
								case "22":
								 $rand_value = "v";
								break;
								case "23":
								 $rand_value = "w";
								break;
								case "24":
								 $rand_value = "x";
								break;
								case "25":
								 $rand_value = "y";
								break;
								case "26":
								 $rand_value = "z";
								break;
								case "27":
								 $rand_value = "0";
								break;
								case "28":
								 $rand_value = "1";
								break;
								case "29":
								 $rand_value = "2";
								break;
								case "30":
								 $rand_value = "3";
								break;
								case "31":
								 $rand_value = "4";
								break;
								case "32":
								 $rand_value = "5";
								break;
								case "33":
								 $rand_value = "6";
								break;
								case "34":
								 $rand_value = "7";
								break;
								case "35":
								 $rand_value = "8";
								break;
								case "36":
								 $rand_value = "9";
								break;
							  }
							return $rand_value;
							}

							function get_rand_id($length)
							{
							  if($length>0) 
							  { 
							  $rand_id="";
							   for($i=1; $i<=$length; $i++)
							   {
							   mt_srand((double)microtime() * 1000000);
							   $num = mt_rand(1,36);
							   $rand_id .= assign_rand_value($num);
							   }
							  }
							return $rand_id;
							}
                        $ran_val=get_rand_id(16);
						$sql_update = mysql_query("update doctor_stub set Doctor_login_enc_key='$ran_val' where Doctor_mobile_number='$mob_number'", $this->db);
						if($sql_update=='success'){
						
						   $sql_mod_enc = mysql_query("SELECT * FROM doctor_stub WHERE Doctor_mobile_number = '$mob_number' AND BINARY Doctor_password = md5('$password') LIMIT 1", $this->db);
					     if(mysql_num_rows($sql_mod_enc) > 0){
						 $result_mod_enc = mysql_fetch_array($sql_mod_enc,MYSQL_ASSOC);
						 }
						 
							$success = array('status' => "Success","doc_details" => $result_mod_enc);
							$this->response($this->json($success),200);
				        }
						else
						{
						   $error = array('status' => "Failed", "msg" => "Error while generating login_encrypted_key");
						   $this->response($this->json($error), 400);
						}
						//$this->response($this->json($result), 200);
					}
					else
					{
					  $error1 = array('status' => "Failed", "msg" => "Login Failure");
					  $this->response($this->json($error1), 204);	// If no records "No Content" status
                    }					  
			}
			else
			{
			
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid mobile number or Password");
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
	
	$api = new LOGIN_DOCTOR;
	$api->processApi();
?>
?>