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

    // Open WhatsApp in a new tab and immediately show thankyou.php
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Redirecting...</title>
        <script>
            // Open WhatsApp in a new tab
            window.open('$waUrl', '_blank');
            // Immediately redirect current tab to thankyou.php
            window.location.href = 'thankyou.php';
        </script>
    </head>
    <body>
        <p>Redirecting you to WhatsApp and showing confirmation...</p>
    </body>
    </html>";
}
?>
