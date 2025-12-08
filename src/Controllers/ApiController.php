<?php

namespace App\Controllers;

use App\Utils\Logger;

class ApiController
{
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function success($data = null, $message = 'Success')
    {
        $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function error($message = 'An error occurred', $statusCode = 400)
    {
        Logger::warning("API Error: $message", [
            'status_code' => $statusCode,
            'controller' => get_class($this)
        ]);
        
        $this->jsonResponse([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}

