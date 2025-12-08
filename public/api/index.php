<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Utils\EnvLoader;
EnvLoader::load();

use App\Routing\Router;
use App\Controllers\TicketController;
use App\Utils\Logger;
set_error_handler(function($severity, $message, $file, $line) {
    Logger::error("PHP Error: $message", [
        'severity' => $severity,
        'file' => $file,
        'line' => $line
    ]);
});

// Set exception handler
set_exception_handler(function($exception) {
    Logger::exception($exception);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $exception->getMessage()
    ]);
    exit;
});

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/api/') === 0) {
    $path = substr($path, 5);
} elseif (strpos($path, '/api') === 0) {
    $path = substr($path, 4);
}

$path = trim($path, '/');

$router = new Router();
$router->get('', [TicketController::class, 'index']);
$router->get('tickets', [TicketController::class, 'index']);
$router->get('tickets/{id}', [TicketController::class, 'show']);
$router->post('tickets', [TicketController::class, 'create']);
$router->put('tickets/{id}', [TicketController::class, 'update']);
$router->delete('tickets/{id}', [TicketController::class, 'delete']);

try {
    if (!$router->dispatch($requestMethod, $path)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
    }
} catch (\Exception $e) {
    Logger::exception($e, [
        'request_method' => $requestMethod,
        'request_path' => $path
    ]);
    
    http_response_code(500);
    $config = require __DIR__ . '/../../config/app.php';
    $errorMessage = $config['debug'] ? $e->getMessage() : 'Server error';
    
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $errorMessage
    ]);
}
