<?php 
class Logger{ 

 private $fileName; 
 private $filePath; 
 private $fileMode; 
 public $file; 
  
 public function __construct($fileName="Logger",$filePath="logs/",$fileMode="a+",$timeZone="Asia/Kolkata"){ 
    date_default_timezone_set($timeZone); 
    $date = date("Ymd"); 
    $this->fileName = $date.$fileName.".log"; 
    $this->filePath = $filePath; 
    $this->file = fopen($this->filePath.$this->fileName,$fileMode); 
    $this->write("INFO :","------------------------------------------");
    $this->write("INFO :","Date =>".date("Y-m-d H:i:s"));
    $this->write("INFO :","Remote Address =>".$_SERVER['REMOTE_ADDR']);
    
    if($this->file==null){ 
      trigger_error("Error: error while creating the file", E_USER_ERROR); 
    }else { 
      fclose($this->file); 
    }

 } 
  
 public function write($type="error",$mensaje=""){ 
    $this->file = fopen($this->filePath.$this->fileName,"a+"); 
     
    if($this->file==null){ 
      trigger_error("Error: No file", E_USER_ERROR); 
    }else { 
      fwrite($this->file,$type." ".$mensaje."\n");
    } 
   } 
}
?>