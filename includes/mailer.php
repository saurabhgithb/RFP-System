<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// load env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$mail = new PHPMailer(true); // create new php mail object, pass true to enable exceptions

// using smtp server with auth
$mail->isSMTP();
$mail->SMTPAuth = true;

// config
$mail->Host = $_ENV["MAILER_HOST"];
$mail->Username = $_ENV["MAILER_USERNAME"];
$mail->Password = $_ENV["MAILER_PASSWORD"];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
$mail->Port = 465;

$mail->isHTML(true); // enable html content

return $mail;

?>