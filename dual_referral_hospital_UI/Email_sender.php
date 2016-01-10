<?php 

class EMAIL_SENDER{

private $to_email;
private $from_email;
private $message;

public function __construct(){
	/*include 'logger.php';
	$this->logger = new Logger();*/
	
}

public function send_email($to,$message){
	 
	 $from="ReferraliO Administrator";
	 $subject="ReferraliO";
	 //$header= "From:".$from ." \r\n";
	 $headers = "MIME-Version: 1.0\r\n";  
     $headers .= "Content-type: text/plain; charset=utf-8\r\n";  
     $headers .= "To: ".$to." <".$to.">\r\n";  
     $headers .= "From: ".$from." <".$from.">\r\n";
     $headers .= "Reply-To: ".$from." <".$from.">\r\n";  
     $headers .= "Return-Path: ".$from." <".$from.">\r\n";  
     $headers .= "\r\n";
	 mail ($to,$subject,$message,$headers);
	 //$this->logger->write("INFO :"," email sender return value".$retval);
	 //return $retval;
   }

}


?>