<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$to = "nikunjrajpara29@gmail.com";
$subject = "Test email from XAMPP";
$body = "This is a test email.";
$headers = "From: yourgmail@gmail.com\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo "Mail sent successfully!";
} else {
    echo "Mail sending failed.";
}
?>
