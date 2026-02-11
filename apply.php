<?php
// Include PHPMailer classes
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $service = $_POST['service'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kinaraittechnician@gmail.com';   // your Gmail
        $mail->Password   = 'vlbd ycqo wudd viir';            // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients (to you)
        $mail->setFrom('kinaraittechnician@gmail.com', 'Kinara Services');
        $mail->addAddress('kinaraittechnician@gmail.com'); // send to yourself

        // Content (to you)
        $mail->isHTML(true);
        $mail->Subject = 'New Service Application';
        $mail->Body    = "
            <h3>New Application Received</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Service:</strong> $service</p>
            <p><strong>Message:</strong> $message</p>
        ";

        $mail->send();

        // Confirmation email to applicant
        $confirm = new PHPMailer(true);
        $confirm->isSMTP();
        $confirm->Host       = 'smtp.gmail.com';
        $confirm->SMTPAuth   = true;
        $confirm->Username   = 'kinaraittechnician@gmail.com';
        $confirm->Password   = 'vlbd ycqo wudd viir';
        $confirm->SMTPSecure = 'tls';
        $confirm->Port       = 587;

        $confirm->setFrom('kinaraittechnician@gmail.com', 'Kinara Services');
        $confirm->addAddress($email, $name);

        $confirm->isHTML(true);
        $confirm->Subject = 'We Received Your Application';
        $confirm->Body    = "
            <h3>Thank you, $name!</h3>
            <p>Your application for <strong>$service</strong> has been received successfully.</p>
            <p>We will review your request and get back to you shortly.</p>
            <p>Best regards,<br>Kinara Services Team</p>
        ";

        $confirm->send();

        // Redirect to thank you page
        header("Location: thankyou.php");
        exit();
    } catch (Exception $e) {
        echo "There was an error sending your application. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
