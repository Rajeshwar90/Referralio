<?php

/*
 * Developed By Rajeshwar Bose
 *
 * PHP: 5.4.37 Date: 05-26-2015
 */
require_once ("Rest.inc.php");
class UNIVERSAL_DOC_API_ADD_DEL extends REST {
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
	 * Simple listing API
	 *
	 * POST method
	 *
	 * enc_key : <Encrypted KEY>
	 *
	 * login_id : <User id autoincremented>
	 *
	 */
	private function ADD_DEL_DOC() {
		error_reporting ( 0 );
		
		$this->logger->write ( "INFO :", "Calling ADD_DEL_DOC for doctor" );
		
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		
		if ($this->get_request_method () != "POST") {
			
			$this->response ( '', 406 );
		}
		
		$post = json_decode ( file_get_contents ( "php://input" ), true );
		
		$login_encrypted_key = $post ['enc_key'];
		
		$login_id = $post ['login_id'];
		
		$doc_id = $post ['doc_id'];
		
		$flag = $post ['flag'];
		
		$this->logger->write ( "INFO :", "login with" . $login_encrypted_key . "login_id" . $login_id );
		
		// $mob_number = $this->_request['Doctor_mobile_number'];
		
		// $password = $this->_request['pwd'];
		
		// Input validations
		
		if (! empty ( $login_encrypted_key ) and ! empty ( $login_id ) and ! empty ( $doc_id )) {
			
			$sql = mysql_query ( "SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id'", $this->db );
			
			if (mysql_num_rows ( $sql ) > 0) {
				
				if ($flag == 'insert') {
					
					/* check for preinsert */
					
					$chk_insert = mysql_query ( "select * from doctor_personal_list where doc_id='$login_id' and personal_list_doc_id='$doc_id'", $this->db );
					
					if (mysql_num_rows ( $chk_insert ) > 0) {
						
						$error = array (
								'status' => "Failure",
								"msg" => "Doctor already added in the list" 
						);
						
						$this->response ( $this->json ( $error ), 500 );
					} 

					else {
						
						$query_insert_doc = mysql_query ( "INSERT INTO doctor_personal_list(doc_id,personal_list_doc_id) VALUES ('$login_id','$doc_id')", $this->db );
						
						if ($query_insert_doc == 'success') {
							
							$this->logger->write ( "INFO :", "login doc_id" . $doc_id );
							
							$success = array (
									'status' => "Success",
									"msg" => "Added to mylist" 
							);
							
							$this->response ( $this->json ( $success ), 200 );
						} 

						else 

						{
							
							$error = array (
									'status' => "Failure",
									"msg" => "Exception failed" 
							);
							
							$this->response ( $this->json ( $error ), 500 );
						}
					}
					
					/* end for check preinsert */
				} 

				else {
					
					$query_delete_doc = mysql_query ( "Delete from doctor_personal_list where doc_id='$login_id' and personal_list_doc_id='$doc_id'", $this->db );
					
					if ($query_delete_doc == 'success') {
						
						$this->logger->write ( "INFO :", "login doc_id" . $doc_id );
						
						$success = array (
								'status' => "Success",
								"msg" => "Deleted from mylist" 
						);
						
						$this->response ( $this->json ( $success ), 200 );
					} 

					else 

					{
						
						$error = array (
								'status' => "Failure",
								"msg" => "Exception failed" 
						);
						
						$this->response ( $this->json ( $error ), 500 );
					}
				}
			} 

			else 

			{
				
				$error1 = array (
						'status' => "Failed",
						"msg" => "Basic authentication failed" 
				);
				
				$this->response ( $this->json ( $error1 ), 203 ); // If no records "No Content" status
			}
		} 

		else {
			
			// If invalid inputs "Bad Request" status message and reason
			
			$error = array (
					'status' => "Failed",
					"msg" => "Either encrypted key or id or doc id is empty" 
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

$api = new UNIVERSAL_DOC_API_ADD_DEL ();

$api->processApi ();

?>