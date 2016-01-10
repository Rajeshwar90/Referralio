<?php
    
	/* 
		This is an example class script proceeding secured API
		To use this class you should keep same as query string and function name
		Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
		     function delete_user(){
				 You code goes here
			 }
		Class will execute the function dynamically;
		
		usage :
		
		    $object->response(output_data, status_code);
			$object->_request	- to get santinized input 	
			
			output_data : JSON (I am using)
			status_code : Send status message for headers
			
		Add This extension for localhost checking :
			Chrome Extension : Advanced REST client Application
			URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo
		
		I used the below table for demo purpose.
		
		CREATE TABLE IF NOT EXISTS 'users' (
		  'user_id' int(11) NOT NULL AUTO_INCREMENT,
		  'user_fullname' varchar(25) NOT NULL,
		  'user_email' varchar(50) NOT NULL,
		  'user_password' varchar(50) NOT NULL,
		  'user_status' tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY ('user_id')
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 	*/
	
	require_once("Rest.inc.php");
	
	class TEST_REST extends REST {
	
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
		
		private function TEST_VAL(){
		    $this->logger->write("INFO :","Calling Update Location for Driver");
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
		
			$val=$this->_request['val'];
			$val1=$_POST['val'];
			
			$this->logger->write("INFO :","login with val ----->".$val);
			$this->logger->write("INFO :","login with val1 ----->".$val1);
			
			if($val !="")
			 {
				$success = array('status' => "Success", "val" => $val);
				$this->response($this->json($success),200);
			 }
			else
			 {
			  $error = array('status' => "error");
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
	
	$api = new TEST_REST;
	$api->processApi();
?>
?>