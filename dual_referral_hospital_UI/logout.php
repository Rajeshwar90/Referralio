<?php
session_start();
session_destroy();

header("Location: admin_main_panel.php");
die();

?>