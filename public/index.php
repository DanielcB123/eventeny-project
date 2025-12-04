<?php
/**
 * Main entry point for the application
 */

// Display a simple welcome message
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>PHP App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .info {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Product Engineering Interview Project</h1>
        <p>Eventeny Project Setup</p>
        <div class='info'>
            <strong>PHP Version:</strong> " . phpversion() . "<br>
            <strong>Server:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . "
        </div>
    </div>
</body>
</html>";
?>

