<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer classes
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // Server settings (from Render environment variables)
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = 'tls'; // try 'ssl' if TLS fails
        $mail->Port       = getenv('SMTP_PORT');

        // Debugging (logs will appear in Render Logs)
        $mail->SMTPDebug  = 2;
        $mail->Debugoutput = 'error_log';

        // Recipients (to you)
        $mail->setFrom(getenv('SMTP_USER'), 'Kinara Services');
        $mail->addAddress(getenv('SMTP_USER'));

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

        if ($mail->send()) {
            // Confirmation email to applicant
            $confirm = new PHPMailer(true);
            $confirm->isSMTP();
            $confirm->Host       = getenv('SMTP_HOST');
            $confirm->SMTPAuth   = true;
            $confirm->Username   = getenv('SMTP_USER');
            $confirm->Password   = getenv('SMTP_PASS');
            $confirm->SMTPSecure = 'tls';
            $confirm->Port       = getenv('SMTP_PORT');

            $confirm->SMTPDebug  = 2;
            $confirm->Debugoutput = 'error_log';

            $confirm->setFrom(getenv('SMTP_USER'), 'Kinara Services');
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

            // Redirect only if thankyou.php exists
            if (file_exists(__DIR__ . '/thankyou.php')) {
                header("Location: thankyou.php");
                exit();
            } else {
                echo "Application submitted successfully, but thankyou.php not found.";
            }
        } else {
            echo "Mailer failed: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        echo "There was an error sending your application. Exception: {$e->getMessage()}";
    }
}
?>
