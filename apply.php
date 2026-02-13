<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate phone number: must start with 07 or 01 and be exactly 10 digits
    if (!preg_match('/^(07\d{8}|01\d{8})$/', $phone)) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Invalid Phone Number</title>
            <style>
                body { font-family: Arial, sans-serif; background: #ff9800; color: #fff; text-align: center; padding: 50px; }
                .error-box { background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; display: inline-block; }
                a { color: #fff; font-weight: bold; text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <h2>Invalid Phone Number</h2>
                <p>Please enter a valid Kenyan phone number starting with 07 or 01 and exactly 10 digits.</p>
                <a href='index.html'>Go Back to Form</a>
            </div>
        </body>
        </html>";
        exit;
    }

    // Build WhatsApp message
    $whatsappMessage = "New Application:\n"
        . "Name: $name\n"
        . "Phone: $phone\n"
        . "Service: $service\n"
        . "Message: $message";

    $whatsappNumber = "254748956783";
    $waUrl = "https://wa.me/$whatsappNumber?text=" . urlencode($whatsappMessage);

    // Pass data to thankyou.php via query string
    $redirectUrl = "thankyou.php?name=" . urlencode($name) .
                   "&phone=" . urlencode($phone) .
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
