<?php
/**
 * Main entry point for the application
 * Eventeny Project
 */

// Set content type
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Eventeny Project - Product Engineering Interview Project">
    <meta name="author" content="Eventeny">
    <title>Eventeny Project</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/css/main.css">
    
    <!-- Preconnect to external resources for performance -->
    <link rel="preconnect" href="https://code.jquery.com">
</head>
<body>
    <div class="container-sm">
        <div class="card">
            <div class="card-header">
                <h1>Product Engineering Interview Project</h1>
            </div>
            <div class="card-body">
                <p>Eventeny Project Setup</p>
                <div class="info-box">
                    <strong>PHP Version:</strong> <span id="php-version"><?php echo htmlspecialchars(phpversion()); ?></span><br>
                    <strong>Server:</strong> <span id="server-info"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'CLI'); ?></span>
                </div>
            </div>
            <div class="card-footer">
                <p class="text-center" style="color: var(--text-secondary); font-size: 0.875rem;">
                    Ready to build amazing features!
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <!-- jQuery (latest version from CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
    
    <!-- Fallback if CDN fails -->
    <script>
        window.jQuery || document.write('<script src="/js/vendor/jquery-3.7.1.min.js"><\/script>');
    </script>
    
    <!-- Main Application JavaScript -->
    <script src="/js/main.js"></script>
</body>
</html>
