<?php
// CORS headers to allow requests from your Netlify domain
header("Access-Control-Allow-Origin: *"); // You can replace * with your Netlify URL for better security
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Handle preflight requests
}

// Manually include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Use the proper namespaces for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure these fields are passed from the form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';

    // --- Database Storage ---
    // Update these with your online database credentials
    $db_host = 'localhost'; // Usually 'localhost' for cPanel or the specific DB host
    $db_user = 'root';      // Your DB username
    $db_pass = '';          // Your DB password
    $db_name = 'your_database_name'; // Your database name

    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Store in DB if connection is successful
    if (!$conn->connect_error) {
        // Table structure: name, email, phone, message, submitted_at
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    // --- PHPMailer ---
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ranjitrautaray475@gmail.com';
        $mail->Password   = 'xeic ubof gibs euzo'; // Note: Use App Password for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // ----- Email to the client -----
        $mail->setFrom('ranjitrautaray475@gmail.com', 'Contact Form');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Thank you for contacting me!';
        $mail->Body    = "Hello $name,<br><br>Thank you for contacting me! I will get back to you soon.<br><br>
                          <strong>My Details:</strong><br>
                          Name: Ranjit Rautaray<br>
                          Email: ranjitrautaray475@gmail.com<br>
                          Phone: +91 9692094475<br><br>
                          You can call contact me at +91 9692094475 or email me at ranjitrautaray475@gmail.com";

        $mail->send();

        // ----- Email to the admin -----
        $mail->clearAddresses();
        $mail->addAddress('ranjitrautaray475@gmail.com', 'Ranjit Rautaray');

        $mail->isHTML(true);
        $mail->Subject = 'New Form Submission';
        $mail->Body    = "You have received a new form submission.<br><br>
                          <strong>Name:</strong> $name<br>
                          <strong>Email:</strong> $email<br>
                          <strong>Phone:</strong> $phone<br>
                          <strong>Message:</strong><br>$message";

        $mail->send();

        echo 'Message has been sent to both client and admin!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
