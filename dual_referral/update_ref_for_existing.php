<?php
require_once ("Rest.inc.php");
class UPDATE_REF_FOR_EXISTING extends REST {
	public $data = "";
	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "refSqlRef007";
	const DB = "referralapp";
	private $db = NULL;
	public function __construct() {
		parent::__construct (); // Init parent contructor
		
		$this->dbConnect (); // Initiate Database connection
		
		include_once ("db_functions.php");
		
		include_once ("GCM.php");
		
		include_once ("logger.php");
		
		include_once("iosPushUniversal.php");
		
		$this->db_func = new DB_Functions ();
		
		$this->gcm = new GCM ();
		
		$this->logger = new Logger ();
		
		$this->obj = new iosPushUniversal();
		
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
	 * Simple registration API
	 *
	 * Registration must be POST method
	 *
	 */
	private function UPDATE_REF_EXISTING() {
		error_reporting ( 0 );
		
		$this->logger->write ( "INFO :", "Calling UPDATE_REF_EXISTING for patient" );
		
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		
		if ($this->get_request_method () != "POST") {
			
			$this->response ( '', 406 );
		}
		
		$post = json_decode ( file_get_contents ( "php://input" ), true );
		
		$login_encrypted_key = $post ['enc_key'];
		
		$login_id = $post ['login_id'];
		
		$this->logger->write ( "INFO :", "login with" . $login_encrypted_key . "login_id" . $login_id );
		
		// Input validations
		
		if (! empty ( $login_encrypted_key ) and ! empty ( $login_id )) {
			
			$sql = mysql_query ( "SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND BINARY Doctor_serial_id = '$login_id'", $this->db );
			
			if (mysql_num_rows ( $sql ) > 0) {
				
				/*
				 * $Patient_Name = $post['Patient_Name'];
				 *
				 * $Patient_Age=$post['Patient_Age'];
				 *
				 * $Patient_Location = $post['Patient_Location'];
				 *
				 * $Patient_issue = $post['Patient_issue'];
				 *
				 * $Patient_note = $post['Patient_note'];
				 *
				 * $Patient_Gender=$post['Patient_Gender'];
				 */
				
				$doc_ref_id = $post ['doc_ref_id'];
				
				$Patient_thread_id = $post ['Patient_thread_id'];
				
				if ($doc_ref_id == "" || $Patient_thread_id == "") 

				{
					
					$error = array (
							'status' => "Failed",
							"msg" => "Compulsory fields are empty" 
					);
					
					$this->response ( $this->json ( $error ), 400 );
				} 

				else 

				{
					
					// INSERT INTO `patient_stub`(`Patient_serial_id`, `Patient_Name`, `Patient_Age`, `Patient_Location`, `Patient_mobile_number`, `Patient_issue_notes`, `Reg_by_doc`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7])
					
					$sql_insert = mysql_query ( "update patient_stub set doc_ref_id='$doc_ref_id' where Patient_thread_id='$Patient_thread_id'", $this->db );
					
					if ($sql_insert == 'success') 

					{
						
						$this->logger->write ( "INFO :", "login doc_ref_id" . $doc_ref_id );
						
						// for notification push to the reffered doctor
						
						if ($doc_ref_id != 0) 

						{
							
							$this->logger->write ( "INFO :", "login inside doc_ref_id" . $doc_ref_id );
							
							$sql_gcm = mysql_query ( "select gcm_regid from gcm_users gs where gs.mob_number=(select Doctor_mobile_number from doctor_stub where Doctor_serial_id='$doc_ref_id')", $this->db );
							
							$this->logger->write ( "INFO :", "mysql_num_rows inside sql_gcm" . mysql_num_rows ( $sql_gcm ) );
							
							$num = mysql_num_rows ( $sql_gcm );
							
							$this->logger->write ( "INFO :", "num inside num" . $num );
							
							if ($num > 0) {
								
								$result_sql_gcm = mysql_fetch_array ( $sql_gcm, MYSQL_ASSOC );
								
								$gcm_id = $result_sql_gcm ['gcm_regid'];
								
								$this->logger->write ( "INFO :", "login gcm_id" . $gcm_id );
								
								$registatoin_ids = array (
										$gcm_id 
								);
								
								$message = array (
										"msg" => "You have been reffered.Please open the application to view it" 
								);
								
								$result = $this->gcm->send_notification ( $registatoin_ids, $message );
								
								$success = array (
										'status' => "Success",
										"msg" => "Patient successfully reffered",
										"result" => $result 
								);
								
								$this->response ( $this->json ( $success ), 200 );
							}
						} 

						else {
							
							$success = array (
									'status' => "Failure",
									"msg" => "Invalid doc ref id 0" 
							);
							
							$this->response ( $this->json ( $success ), 400 );
						}
					} 

					else 

					{
						
						$error = array (
								'status' => "Failure",
								"msg" => "Patient could not be referred.SQL exception expected" 
						);
						
						$this->response ( $this->json ( $success ), 400 );
					}
				}
			} 

			else 

			{
				
				$error = array (
						'status' => "Failed",
						"msg" => "Authentication failed with the encrypted key" 
				);
				
				$this->response ( $this->json ( $error ), 400 );
			}
		} 

		else 

		{
			
			// If invalid inputs "Bad Request" status message and reason
			
			$error = array (
					'status' => "Failed",
					"msg" => "Encrypted key or id is empty" 
			);
			
			$this->response ( $this->json ( $error ), 400 );
		}
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

$api = new UPDATE_REF_FOR_EXISTING ();

$api->processApi ();

?>