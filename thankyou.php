<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - Kinara Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #004d40, #00796b);
            color: #fff;
            text-align: center;
            padding: 50px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #ffeb3b;
        }
        p {
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #ffeb3b;
            color: #004d40;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #fbc02d;
        }
        .wa-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #25D366; /* WhatsApp green */
            color: #fff;
        }
        .wa-btn:hover {
            background: #128C7E;
        }
        .wa-icon {
            width: 20px;
            height: 20px;
        }
        footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank You!</h1>
        <p>Your application has been submitted successfully via WhatsApp.</p>
        <p>Our team has received your details and will review your request shortly.</p>

        <?php
        // Build fallback WhatsApp link using query parameters
        $name    = $_GET['name'] ?? '';
        $email   = $_GET['email'] ?? '';
        $service = $_GET['service'] ?? '';
        $message = $_GET['message'] ?? '';

        $whatsappMessage = "New Application:\n"
            . "Name: $name\n"
            . "Email: $email\n"
            . "Service: $service\n"
            . "Message: $message";

        $whatsappNumber = "254748956783";
        $waUrl = "https://wa.me/$whatsappNumber?text=" . urlencode($whatsappMessage);
        ?>

        <!-- Fallback WhatsApp link with icon -->
        <p>If WhatsApp didn’t open automatically, click below to send your application:</p>
        <a href="<?php echo $waUrl; ?>" class="btn wa-btn" target="_blank">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="wa-icon">
            Send via WhatsApp
        </a>

        <br><br>
        <a href="index.html" class="btn">Return to Homepage</a>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> Kinara Services. All rights reserved.
    </footer>
</body>
</html>
