<?php
include 'db_conn.php';
session_start ();
// print_r($_SESSION);

if (! isset ( $_SESSION ['hospital_id_SESSION'] )) {
    header ( "Location: hospital_login.php" );
    die ();
}

$hospital_id = $_SESSION ['hospital_id_SESSION'];
$hospital_name = $_SESSION ['hospital_name_SESSION'];

$patient_id=$_GET['patient'];
$newURL='referralio_home.php';
//header('Location: '.$newURL);


/* $file_formats = array("txt", "pdf", "doc"); // Set File format
$filepath = "/dischargeDocuments/";

if ($_POST['submitbtn']=="submit") {
    
    $name = $_FILES['upload_file']['name'];
    //print_r($_FILES);
    $size = $_FILES['upload_file']['size'];
    //echo $size;

    if (strlen($name)) {
        $extension = substr($name, strrpos($name, '.')+1);
        if (in_array($extension, $file_formats)) {
            if ($size < (2048 * 1024)) {
                $imagename = md5(uniqid().time()).".".$extension;
                $tmp = $_FILES['upload_file']['tmp_name'];
                if (move_uploaded_file($tmp, $filepath . $imagename)) {
                    echo 'document is uploaded successfully';
                } else {
                    echo "Could not move the file.";
                }
            } else {
                echo "Your image size is bigger than 2MB.";
            }
        } else {
            echo "Invalid file format.";
        }
    } else {
        echo "Please select image..!";
    }
    exit();
}*/

echo "hello123";
exit();

?>