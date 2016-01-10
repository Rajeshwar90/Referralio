<?php 

class EMAIL_SENDER{

private $to_email;
private $from_email;
private $message;

public function __construct(){
	//include 'logger.php';
	//$this->logger = new Logger();
	
}

public function send_email($to,$message){
	 
	 $from="ReferraliO Administrator";
	 $subject="ReferraliO";
	 $header= "From:".$from ." \r\n";
	 $retval = mail ($to,$subject,$message,$header);
	 //$this->logger->write("INFO :"," email sender return value".$retval);
	 //return $retval;
   }

}


?>