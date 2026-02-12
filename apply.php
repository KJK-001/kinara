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

    // Redirect to an intermediate page that opens WhatsApp, then thankyou.php
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Redirecting...</title>
        <script>
            // Open WhatsApp in a new tab
            window.open('$waUrl', '_blank');
            // After 2 seconds, redirect back to thankyou.php
            setTimeout(function() {
                window.location.href = 'thankyou.php';
            }, 2000);
        </script>
    </head>
    <body>
        <p>Redirecting you to WhatsApp... Please wait.</p>
    </body>
    </html>";
}
?>
