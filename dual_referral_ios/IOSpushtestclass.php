<?php 
require_once 'iosPushUniversal.php';

$obj = new iosPushUniversal();
$registatoin_ids=array(
    '68141f71cbae1c493318f17b132215223655c27a2670a580dee074c4cdf96028'
);

$msg="Hello testing 123";

$obj->sendIosPush($registatoin_ids,$msg);
?>