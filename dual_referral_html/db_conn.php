<?php

$servername="localhost";
$username="root";
$password="refSqlRef007";

mysql_connect($servername,$username,$password);

mysql_select_db('referralapp') or die(mysql_error());

?>