<?php
require_once ("Rest.inc.php");
class UPDATE_DOC_PROFILE extends REST {
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
	private function UPDATE_DOC() {
		error_reporting ( 0 );
		
		$this->logger->write ( "INFO :", "Calling update for doctor" );
		
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		
		if ($this->get_request_method () != "POST") {
			
			$this->response ( '', 406 );
		}
		
		$post = json_decode ( file_get_contents ( "php://input" ), true );
		
		$login_encrypted_key = mysql_real_escape_string($post['enc_key']);
		
		$login_id= mysql_real_escape_string($post['login_id']);
		
		$Doctor_name = mysql_real_escape_string ( $post ['Doctor_name'] );
		
		$Doctor_dob = $post ['Doctor_dob'];
		
		$Doctor_email = mysql_real_escape_string ( $post ['Doctor_email'] ); // can be changed
		
		$Doctor_specialization = mysql_real_escape_string ( $post ['Doctor_specialization'] ); // can be changed
		
		$Doctor_qualification = mysql_real_escape_string ( $post ['Doctor_qualification'] ); // can be changed
		
		$Doctor_HospitalName = mysql_real_escape_string ( $post ['Doctor_HospitalName'] ); // can be changed
		
		$Doctor_mobile_number = $post ['Doctor_mobile_number'];
		
		// $Doctor_password = $post['Doctor_password'];//can be changed
		
		$Doctor_photograph = $post ['Doctor_photograph']; // can be changed
		
		$Doctor_yxp = mysql_real_escape_string ( $post ['Doctor_yxp'] );
		
		$Doctor_Country = mysql_real_escape_string ( $post ['Doctor_Country'] );
		
		$Doctor_State = mysql_real_escape_string ( $post ['Doctor_State'] );
		
		$Doctor_City = mysql_real_escape_string ( $post ['Doctor_City'] );
		
		$Doctor_Address = mysql_real_escape_string ( $post ['Doctor_Address'] );
		
		$license_number=mysql_real_escape_string($post['license_number']);
		$visibility=mysql_real_escape_string($post['visibility']);
		$my_type=mysql_real_escape_string($post['my_type']);
		$country_code=mysql_real_escape_string($post['country_code']);
		
		if ($Doctor_photograph != "") {
			
			$data = str_replace ( 'data:image/png;base64,', '', $Doctor_photograph );
			
			$data = str_replace ( ' ', '+', $data );
			
			$data = base64_decode ( $data );
			
			$imageName = $Doctor_mobile_number . '.png';
			
			$file = 'profile_doc_images/' . $Doctor_mobile_number . '.png';
			
			// $success = file_put_contents($file, $data);
		} else {
			
			$imageName = "account_image.png";
		}
		
		// Input validations
		
		if (! empty ( $Doctor_mobile_number ) and ! empty ( $Doctor_name ) and ! empty ( $Doctor_dob ) and ! empty ( $Doctor_email ) and ! empty ($login_encrypted_key) and ! empty ($login_id)) {
			
			// code to check for uniqueness of mobile number
			$this->logger->write("INFO :","login with".$Doctor_mobile_number);
			
			$sql_mob_unique = mysql_query ( "SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id'", $this->db );
			
			if (mysql_num_rows ( $sql_mob_unique ) == 1) 

			{
				
				$sql_print = "update doctor_stub set Doctor_name='$Doctor_name', Doctor_dob='$Doctor_dob',Doctor_email='$Doctor_email',Doctor_specialization='$Doctor_specialization',Doctor_qualification='$Doctor_qualification',Doctor_HospitalName='$Doctor_HospitalName',Doctor_Country='$Doctor_Country',Doctor_State='$Doctor_State',Doctor_City='$Doctor_City',Doctor_Address='$Doctor_Address',Doctor_photograph='$imageName',Doctor_yxp='$Doctor_yxp',license_number='$license_number',visibility='$visibility',my_type='$my_type',country_code='$country_code' where Doctor_serial_id='$login_id'";
				
				$this->logger->write ( "INFO :", "PHP sql print =>" . $sql_print );
				
				$sql = mysql_query ( "update doctor_stub set Doctor_name='$Doctor_name', Doctor_dob='$Doctor_dob',Doctor_email='$Doctor_email',Doctor_specialization='$Doctor_specialization',Doctor_qualification='$Doctor_qualification',Doctor_HospitalName='$Doctor_HospitalName',Doctor_Country='$Doctor_Country',Doctor_State='$Doctor_State',Doctor_City='$Doctor_City',Doctor_Address='$Doctor_Address',Doctor_photograph='$imageName',Doctor_yxp='$Doctor_yxp',license_number='$license_number',visibility='$visibility',my_type='$my_type',country_code='$country_code' where Doctor_serial_id='$login_id'", $this->db );
				
				/*
				 * if(mysql_num_rows($sql) > 0){
				 *
				 * $result = mysql_fetch_array($sql,MYSQL_ASSOC);
				 *
				 *
				 *
				 * // If success everythig is good send header as "OK" and user details
				 *
				 * $this->response($this->json($result), 200);
				 *
				 * }
				 *
				 * $this->response('', 204); // If no records "No Content" status
				 *
				 */
				
				if ($sql == 'success') {
					
					if ($Doctor_photograph != "") {
						
						unlink ( 'profile_doc_images/' . $Doctor_mobile_number . '.png' );
						
						$this->logger->write ( "INFO inside file image putting" );
						
						$file_put = file_put_contents ( $file, $data );
					}
					
					$success = array (
							'status' => "Success",
							"msg" => "Successfully doctor has been updated" 
					);
					
					$this->response ( $this->json ( $success ), 200 );
				} 

				else 

				{
					
					$error = array (
							'status' => "Failed",
							"msg" => "SQL query failed" 
					);
					
					$this->response ( $this->json ( $error ), 400 );
				}
			} 

			else 

			{
				
				$error = array (
						'status' => "Failed",
						"msg" => "updation failed due to query count" 
				);
				
				$this->response ( $this->json ( $error ), 400 );
			}
		} 

		else 

		{
			
			// If invalid inputs "Bad Request" status message and reason
			
			$error = array (
					'status' => "Failed",
					"msg" => "Invalid inputs provided" 
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

$api = new UPDATE_DOC_PROFILE ();

$api->processApi ();

?>