<?php

require_once 'parameters.php';

class iosPushUniversal {
    private $file;
    private $iosServer;
    private $pemPwd;
    //put your code here
    // constructor
    function __construct() {
        $this->file = IOSPEMFILE;
        $this->iosServer = IOSPUSHSERVER;
        $this->pemPwd = IOSPEMPWD;
    }
 
    /**
     * Sending Push Notification
     */
    public function sendIosPush($registatoin_ids, $message) {
        
        set_time_limit(0);
        
        header('content-type: text/html; charset: utf-8');
        $passphrase = 'password';
        //$message = tr_to_utf($message);
        $deviceIds = array(
                '68141f71cbae1c493318f17b132215223655c27a2670a580dee074c4cdf96028'
        );
        $payload = '{"aps":{"alert":"' . $message . '","sound":"default"}}';
        $result = 'Start' . '<br />';
        
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        
        foreach ($deviceIds as $item) {
            //sleep(1);
            $fp = stream_socket_client($this->iosServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $item) . pack('n', strlen($payload)) . $payload;
             
            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            if ($fp) {
                fclose($fp);
            }
        }
        //set_time_limit(30);
        
    }
    
    public function tr_to_utf($text) {
            $text = trim($text);
            $search = array('Ü', 'Þ', 'Ð', 'Ç', 'Ý', 'Ö', 'ü', 'þ', 'ð', 'ç', 'ý', 'ö');
            $replace = array('Ãœ', 'Åž', '&#286;ž', 'Ã‡', 'Ä°', 'Ã–', 'Ã¼', 'ÅŸ', 'ÄŸ', 'Ã§', 'Ä±', 'Ã¶');
            $new_text = str_replace($search, $replace, $text);
            return $new_text;
        }
 
}
 
?>