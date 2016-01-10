<?php $name = $_POST['first_name'];
$email = $_POST['email'];
$message = $_POST['message'];
$phone = $_POST['phone'];
$formcontent="From: $name \n\n Email: $email \n\n phone:$phone \n\n Message: $message";
$recipient = "shashi@medisense.me";
$subject = "Referralio";
$mailheader = "From: $email \r\n";
mail($recipient, $subject, $formcontent, $mailheader) or die("Error!");



 // $message2="Thank you";
    // mail($mailheader,$subject,$message2,$recipient) or die("Error!"); // sends a copy of the message to the sender
   
   
?>
