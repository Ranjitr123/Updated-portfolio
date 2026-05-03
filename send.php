<?php
// Allow same-host and CORS requests if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$phone || !$address || !$message) {
    http_response_code(422);
    echo 'All fields are required.';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo 'Please enter a valid email address.';
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ranjitrautaray475@gmail.com';
    $mail->Password   = 'llfx vvym zddc ogub';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Send notification to admin
    $mail->setFrom('ranjitrautaray475@gmail.com', 'Portfolio Contact Form');
    $mail->addAddress('ranjitrautaray475@gmail.com', 'Ranjit Rautaray');
    $mail->addReplyTo($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'New contact form message from ' . $name;
    $mail->Body    = "<h2>New contact form submission</h2>"
                   . "<p><strong>Name:</strong> {$name}</p>"
                   . "<p><strong>Email:</strong> {$email}</p>"
                   . "<p><strong>Phone:</strong> {$phone}</p>"
                   . "<p><strong>Address:</strong> {$address}</p>"
                   . "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
    $mail->send();

    // Send an optional autoresponder to visitor
    $mail->clearAddresses();
    $mail->clearReplyTos();
    $mail->addAddress($email, $name);
    $mail->Subject = 'Thank you for contacting me';
    $mail->Body    = "<p>Hi {$name},</p>"
                   . "<p>Thanks for reaching out! I have received your message and will get back to you soon.</p>"
                   . "<p><strong>Address:</strong> {$address}</p>"
                   . "<p><strong>Your message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>"
                   . "<p>Best regards,<br>Ranjit Rautaray</p>";
    $mail->send();

    echo 'Message has been sent successfully.';
} catch (Exception $e) {
    http_response_code(500);
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
