<?php

/*
 * Developed By Rajeshwar Bose
 * PHP: 5.4.37 Date: 05-26-2015
 */
require_once ("Rest.inc.php");
require_once 'parameters.php';
class GETMYCLIENTS extends REST {
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
        include_once ("iosPushUniversal.php");
        $this->logger = new Logger ();
        $this->obj = new iosPushUniversal ();
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
     * Simple login API
     * Login must be POST method
     * email : <USER MOBILE NUMBER>
     * pwd : <USER PASSWORD>
     */
    private function GET_CLIENT_SALES() {
        error_reporting ( 0 );
        
        $this->logger->write ( "INFO :", "Calling LOGIN_SALES for salesperson" );
        
        // Cross validation if the request method is POST else it will return "Not Acceptable" status
        
        if ($this->get_request_method () != "POST") {
            
            $this->response ( '', 406 );
        }
        
        $post = json_decode ( file_get_contents ( "php://input" ), true );
        
        $login_encrypted_key = $post ['enc_key'];
        
        $login_id = $post ['login_id'];
        $user="hospital_user";
        $user_all="hospital_user_all";
        
        $this->logger->write ( "INFO :", "login with" . $login_encrypted_key . "login_id" . $login_id );
        
        // $mob_number = $this->_request['Doctor_mobile_number'];
        
        // $password = $this->_request['pwd'];
        
        // Input validations
        
        if (! empty ( $login_encrypted_key ) and ! empty ( $login_id )) {
            $sql = mysql_query ( "SELECT * FROM doctor_stub WHERE BINARY Doctor_login_enc_key = '$login_encrypted_key' AND Doctor_serial_id = '$login_id' AND type_value in ('$user','$user_all')", $this->db );
            
            if (mysql_num_rows ( $sql ) > 0) {
                
            	while($row_sql=mysql_fetch_assoc($sql)){
            		$typeval=$row_sql['type_value'];
            	}
            	
            	$this->logger->write ( "INFO :", "type value".$typeval );
            	
                // query to give the list of patients
                //$query_get_clients="SELECT * FROM `referral_mapping_patient_stub` rm inner join patient_stub ps on rm.patient_stub_id=ps.Patient_thread_id  where rm.mapping_hospital_user_id='$login_id' order by rm.timestamp DESC";
                
            	if($typeval=='hospital_user'){
            		
            		$query_get_clients="select doctor_name,temp1.* from doctor_stub ds inner join (SELECT mapping_hospital_user_id,mapping_id,patient_stub_id,sales_view_flag,rm.timestamp as time, Patient_thread_id, Patient_Name,Patient_Age,Patient_Gender,Patient_Location,Patient_mobile_number,Patient_issue_notes,Reg_by_doc,Patient_defined_notes,doc_ref_id FROM `referral_mapping_patient_stub` rm inner join patient_stub ps on rm.patient_stub_id=ps.Patient_thread_id  where rm.mapping_hospital_user_id='$login_id' order by rm.timestamp DESC)as temp1 on ds.Doctor_serial_id=temp1.Reg_by_doc";
            		
            	}else if ($typeval=='hospital_user_all'){
            		
            		//$query_get_clients="select * from patient_stub where hospital_id_transaction=(SELECT doctor_yxp from doctor_stub where type_value='hospital_user_all' and doctor_serial_id='$login_id')";
            		//$query_get_clients="select 'Undefined' as doctor_name,'Undefined' as mapping_hospital_user_id,'Undefined' as mapping_id,Patient_thread_id as patient_stub_id,'0' as sales_view_flag,Patient_thread_id,Patient_Name,Patient_Age,Patient_Gender,Patient_Location,Patient_mobile_number,Patient_issue_notes,Reg_by_doc,Patient_defined_notes,doc_ref_id,timestamp as time from patient_stub where hospital_id_transaction=(SELECT doctor_yxp from doctor_stub where type_value='hospital_user_all' and doctor_serial_id='$login_id')";
            		$query_get_clients="select temp.*,ds.doctor_name from (select 'Undefined' as mapping_hospital_user_id,'Undefined' as mapping_id,Patient_thread_id as patient_stub_id,'0' as sales_view_flag,Patient_thread_id,Patient_Name,Patient_Age,Patient_Gender,Patient_Location,Patient_mobile_number,Patient_issue_notes,Reg_by_doc,Patient_defined_notes,doc_ref_id,timestamp as time from patient_stub where hospital_id_transaction=(SELECT doctor_yxp from doctor_stub where type_value='hospital_user_all' and doctor_serial_id='$login_id'))as temp inner join doctor_stub ds on temp.Reg_by_doc=ds.Doctor_Serial_id";
            	}
                
               
                
                $this->logger->write ( "INFO :", "get clients query".$query_get_clients );
                $res_get_clients=mysql_query($query_get_clients,$this->db);
                
                if (mysql_num_rows ( $res_get_clients ) > 0) {
                    	
                    while ( $row_get_clients = mysql_fetch_assoc ( $res_get_clients ) ) {
                
                        $json [] = $row_get_clients;
                    }
                    
                    /* //push for count of new patient view flag
                    $query_push_count_pat=mysql_query("SELECT count(*) as cnt FROM `referral_mapping_patient_stub` rm where rm.mapping_hospital_user_id='$login_id' and rm.sales_view_flag=0 ",$this->db);
                    $row_push_cnt_view=mysql_fetch_assoc($query_push_count_pat);
                    $count_new_pat_client=$row_push_cnt_view['cnt']; */
                    
                    /* $sql_gcm=mysql_query("select gcm_regid from gcm_users gs where gs.mob_number='$login_id'",$this->db);
                    if(mysql_num_rows($sql_gcm)>0){
                        $row_gcm=mysql_fetch_assoc($sql_gcm);
                        $gcm_reg=$row_gcm['gcm_regid'];
                        $mobile_os_type=$row_gcm['mobile_os_type'];
                        if($mobile_os_type=="Android"){
                            $this->logger->write ( "INFO :", "inside Android");
                            $registatoin_ids = array($gcm_reg);
                            $message = array("Pat_client_cnt" => $count_new_pat_client);
                            $result = $this->gcm->send_notification($registatoin_ids, $message);
                            
                        }else if($mobile_os_type=="IOS"){
                            //need to call proper function
                            $this->logger->write ( "INFO :", "inside IOS");
                            $registatoin_ids = array($gcm_reg);
                            $message = array("Pat_client_cnt" => $count_new_pat_client);
                            $result = $this->gcm->send_notification($registatoin_ids, $message);
                            $this->obj->sendIosPush();
                        }
                    } */
                    
                    
                    //updating the sales view flag
                    if($typeval=='hospital_user'){
                    	$query_update_slave_view=mysql_query("update referral_mapping_patient_stub set sales_view_flag=1 where mapping_hospital_user_id='$login_id'",$this->db);
                    }
                    
                    
                    
                    	
                    $success = array (
                            'status' => "Success",
                            "msg" => "Client Patients available",
                            "Pat_list" => $json
                    );
                    	
                    $this->response ( $this->json ( $success ), 200 );
                }
                
                else
                
                {
                    	
                    $success = array (
                            'status' => "Success",
                            "msg" => "Patients Not available"
                    );
                    	
                    $this->response ( $this->json ( $success ), 204 );
                }
                
                
            } else 

            {
                
                $error1 = array (
                        'status' => "Failed",
                        "msg" => "Basic authentication failed" 
                );
                
                $this->response ( $this->json ( $error1 ), 203 ); // If no records "No Content" status
            }
        } else {
            
            // If invalid inputs "Bad Request" status message and reason
            
            $error = array (
                    'status' => "Failed",
                    "msg" => "Either encrypted key or id is empty" 
            );
            
            $this->response ( $this->json ( $error ), 400 );
        }
    }
    
    /*
     * Encode array into JSON
     */
    private function json($data) {
        if (is_array ( $data )) {
            return json_encode ( $data );
        }
    }
}

// Initiiate Library

$api = new GETMYCLIENTS ();
$api->processApi ();
?>
