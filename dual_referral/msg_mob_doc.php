<?php

/*
 * Developed By Rajeshwar Bose
 *
 * PHP: 5.4.37 Date: 05-26-2015
 */
require_once ("Rest.inc.php");
class MSG_MOB_DOC extends REST {
	public $data = "";
	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "refSqlRef007";
	const DB = "referralapp";
	private $db = NULL;
	public function __construct() {
		parent::__construct (); // Init parent contructor
		
		$this->dbConnect (); // Initiate Database connection
		
		include_once ("logger.php");
		
		$this->logger = new Logger ();
		
		$this->logger->write ( "INFO :", "PHP Scritp Name =>" . $_SERVER ['REQUEST_URI'] );
		
		$this->logger->write ( "INFO :", "Type of Request =>" . $_SERVER ['REQUEST_METHOD'] );
	}
	
	/*
	 *
	 * Database connection
	 *
	 */
	private function dbConnect() {
		$this->db = mysql_connect ( self::DB_SERVER, self::DB_USER, self::DB_PASSWORD );
		
		if ($this->db)
			
			mysql_select_db ( self::DB, $this->db );
	}
	
	/*
	 *
	 * Public method for access api.
	 *
	 * This method dynmically call the method based on the query string
	 *
	 *
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
	 *
	 * Simple login API
	 *
	 * Login must be POST method
	 *
	 * email : <USER MOBILE NUMBER>
	 *
	 * pwd : <USER PASSWORD>
	 *
	 */
	private function MSG_MOB() {
		error_reporting ( 0 );
		
		$this->logger->write ( "INFO :", "Calling MSG_MOB for doctor" );
		
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		
		if ($this->get_request_method () != "POST") {
			
			$this->response ( '', 406 );
		}
		
		$post = json_decode ( file_get_contents ( "php://input" ), true );
		
		$login_encrypted_key = $post ['enc_key'];
		
		$login_id = $post ['login_id'];
		
		$doctor_name = mysql_real_escape_string ( $post ['doctor_name'] );
		
		$mobile = $post ['mobile'];
		
		$patient_name = $post ['patient_name'];
		
		$patient_mobile = $post ['patient_mobile'];
		
		$this->logger->write ( "INFO :", "login with enc_key" . $login_encrypted_key . "login_id" . $login_id );
		
		// $mob_number = $this->_request['Doctor_mobile_number'];
		
		// $password = $this->_request['pwd'];
		
		// Input validations
		
		if (! empty ( $login_encrypted_key ) and ! empty ( $login_id )) {
			
			$sql = mysql_query ( "SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id'", $this->db );
			
			if (mysql_num_rows ( $sql ) > 0) {
				
				$query_insert_doc = mysql_query ( "INSERT INTO doc_msg_api(login_id,doctor_name, mobile_number ,patient_name,patient_mobile_number) VALUES ('$login_id','$doctor_name','$mobile','$patient_name','$patient_mobile')", $this->db );
				
				if ($query_insert_doc == 'success') {
					
					$val = "doctor";
					
					$name_pat = $patient_name;
					
					$mobile_pat = $patient_mobile;
					
					$output = $this->send_msg ( $mobile, $val, $name_pat, $mobile_pat );
					
					// $output=$this->httpget($mobile);
					
					$this->logger->write ( "INFO :", "login with output" . $output );
					
					if ($patient_name != "" && $patient_mobile != "") 

					{
						
						$val1 = "patient";
						
						$name_doc = $doctor_name;
						
						$mobile_doc = $mobile;
						
						$output_patient = $this->send_msg ( $patient_mobile, $val1, $name_doc, $mobile_doc );
						
						$this->logger->write ( "INFO :", "login with patient mobile output" . $output_patient );
					}
					
					$success = array (
							'status' => "Success",
							"msg" => "doctor saved" 
					);
					
					$this->response ( $this->json ( $success ), 200 );
				} else {
					
					$error1 = array (
							'status' => "Failed",
							"msg" => "doctor could not be saved" 
					);
					
					$this->response ( $this->json ( $error1 ), 400 );
				}
			} 

			else 

			{
				
				$error1 = array (
						'status' => "Failed",
						"msg" => "Login Failure" 
				);
				
				$this->response ( $this->json ( $error1 ), 400 ); // If no records "No Content" status
			}
		} 

		else 

		{
			
			// If invalid inputs "Bad Request" status message and reason
			
			$error = array (
					'status' => "Failed",
					"msg" => "Invalid login_encrypted_key or login_id" 
			);
			
			$this->response ( $this->json ( $error ), 400 );
		}
	}
	private function httpget($mobile) {
		$msg = urlencode ( "Please register yourself in this app Doctor Referral APP" );
		
		$host = "http://sms6.routesms.com";
		
		$uri = "http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=7276698941&source=HCHKIN&message=" . $msg;
		
		$uri = "http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=" . $mobile . "&source=HCHKIN&message=" . $msg;
		
		$msg = 'GET ' . $uri . " HTTP/1.1\r\n" . 'Host: ' . $host . "\r\n" . "Connection: close\r\n\r\n";
		
		$fh = fsockopen ( $host, 8080 );
		
		fwrite ( $fh, $msg );
		
		$result = '';
		
		while ( ! feof ( $fh ) ) {
			
			$result .= fgets ( $fh );
		}
		
		fclose ( $fh );
		
		return $result;
	}
	private function send_msg($mobile, $receiver, $name, $mobile_msg) {
		$this->logger->write ( "INFO :", "login with mobile" . $mobile );
		
		// $ch = curl_init();
		
		// curl_setopt($ch,CURLOPT_VERBOSE,1);
		
		$msg = "";
		
		if ($receiver == "doctor") {
			
			$msg = urlencode ( "Please register yourself in this app Doctor Referral APP.$You have been referred for " . $name . "(" . $mobile_msg . ")" );
		} else {
			
			$msg = urlencode ( "You have been referred to doctor " . $name . "(" . $mobile_msg . ")" );
		}
		
		$this->logger->write ( "INFO :", "login with msg" . $msg );
		
		$url = "http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=" . $mobile . "&source=HCHKIN&message=" . $msg;
		
		$this->logger->write ( "INFO :", "login with url" . $url );
		
		$ch = curl_init (); // setup a curl
		
		curl_setopt ( $ch, CURLOPT_URL, $url ); // set url to send to
		
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers ); // set custom headers
		
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // return data reather than echo
		
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false ); // required as godaddy fails
		
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		
		$output = curl_exec ( $ch );
		
		// echo "output".$output;
		
		curl_close ( $ch );
		
		return $output;
	}
	
	/*
	 *
	 * Encode array into JSON
	 *
	 */
	private function json($data) {
		if (is_array ( $data )) {
			
			return json_encode ( $data );
		}
	}
}

// Initiiate Library

$api = new MSG_MOB_DOC ();

$api->processApi ();

?>