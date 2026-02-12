<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';

    // Build WhatsApp message
    $whatsappMessage = "New Application:\n"
        . "Name: $name\n"
        . "Email: $email\n"
        . "Service: $service\n"
        . "Message: $message";

    $whatsappNumber = "254748956783";
    $waUrl = "https://wa.me/$whatsappNumber?text=" . urlencode($whatsappMessage);

    // Pass data to thankyou.php via query string
    $redirectUrl = "thankyou.php?name=" . urlencode($name) .
                   "&email=" . urlencode($email) .
                   "&service=" . urlencode($service) .
                   "&message=" . urlencode($message);

    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Redirecting...</title>
        <script>
            // Open WhatsApp in a new tab
            window.open('$waUrl', '_blank');
            // Immediately redirect current tab to thankyou.php with data
            window.location.href = '$redirectUrl';
        </script>
    </head>
    <body>
        <p>Redirecting you to WhatsApp and showing confirmation...</p>
    </body>
    </html>";
}
?>
