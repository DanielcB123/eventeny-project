<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Controllers\ApiController;
use App\Utils\Logger;

class TicketController extends ApiController
{
    private $ticketModel;

    public function __construct()
    {
        $this->ticketModel = new Ticket();
    }

    public function index()
    {
        $publicOnly = isset($_GET['public']) && $_GET['public'] === 'true';
        $tickets = $this->ticketModel->getAll($publicOnly);
        $this->success($tickets);
    }

    public function show($id)
    {
        $ticket = $this->ticketModel->getById($id);
        
        if (!$ticket) {
            $this->error('Ticket not found', 404);
        }

        $this->success($ticket);
    }

    public function create()
    {
        try {
            $data = $this->getRequestData();
            
            if (!$this->validateTicketData($data)) {
                $this->error('Invalid ticket data');
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_path'] = $this->handleImageUpload($_FILES['image']);
            }

            $id = $this->ticketModel->create($data);
            $ticket = $this->ticketModel->getById($id);
            
            $this->success($ticket, 'Ticket created successfully');
        } catch (\Exception $e) {
            Logger::exception($e, ['action' => 'create_ticket']);
            $this->error('Failed to create ticket: ' . $e->getMessage(), 500);
        }
    }

    public function update($id)
    {
        try {
            $data = $this->getRequestData();
            
            if (!$this->validateTicketData($data, false)) {
                $this->error('Invalid ticket data');
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $oldTicket = $this->ticketModel->getById($id);
                if ($oldTicket && $oldTicket['image_path']) {
                    $oldImagePath = __DIR__ . '/../../public' . $oldTicket['image_path'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $data['image_path'] = $this->handleImageUpload($_FILES['image']);
            }

            $this->ticketModel->update($id, $data);
            $ticket = $this->ticketModel->getById($id);
            
            $this->success($ticket, 'Ticket updated successfully');
        } catch (\Exception $e) {
            Logger::exception($e, ['action' => 'update_ticket', 'id' => $id]);
            $this->error('Failed to update ticket: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $ticket = $this->ticketModel->getById($id, true);
            if (!$ticket) {
                $this->error('Ticket not found', 404);
            }
            
            if ($ticket['deleted_at'] !== null) {
                $this->error('Ticket is already deleted', 400);
            }
            
            $this->ticketModel->delete($id);
            $this->success(null, 'Ticket deleted successfully');
        } catch (\Exception $e) {
            Logger::exception($e, ['action' => 'delete_ticket', 'id' => $id]);
            $this->error('Failed to delete ticket: ' . $e->getMessage(), 500);
        }
    }

    private function getRequestData()
    {
        if (!empty($_POST)) {
            $data = $_POST;
            if (isset($data['quantity'])) {
                $data['quantity'] = (int)$data['quantity'];
            }
            if (isset($data['price'])) {
                $data['price'] = (float)$data['price'];
            }
            if (isset($data['is_public'])) {
                $data['is_public'] = (int)$data['is_public'];
            }
        } else {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
                return [];
            }
        }
        
        if (isset($data['sale_start_date'])) {
            $data['sale_start_date'] = $this->convertDateTimeFormat($data['sale_start_date']);
        }
        if (isset($data['sale_end_date'])) {
            $data['sale_end_date'] = $this->convertDateTimeFormat($data['sale_end_date']);
        }
        
        return $data;
    }
    
    private function handleImageUpload($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new \Exception('Invalid file type. Only images are allowed.');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new \Exception('File size exceeds 5MB limit.');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('ticket_', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to upload image.');
        }
        
        return '/uploads/' . $filename;
    }
    
    private function convertDateTimeFormat($dateTime)
    {
        if (empty($dateTime)) {
            return null;
        }
        
        // Already in MySQL format
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateTime)) {
            return $dateTime;
        }
        
        $dateTime = str_replace('T', ' ', $dateTime);
        if (strlen($dateTime) === 16) {
            $dateTime .= ':00';
        }
        
        return $dateTime;
    }

    private function validateTicketData($data, $requireAll = true)
    {
        $required = ['title', 'sale_start_date', 'sale_end_date', 'quantity', 'price'];
        
        foreach ($required as $field) {
            if ($requireAll && !isset($data[$field])) {
                return false;
            }
        }

        if (isset($data['sale_start_date']) && isset($data['sale_end_date'])) {
            if (strtotime($data['sale_start_date']) > strtotime($data['sale_end_date'])) {
                return false;
            }
        }

        if (isset($data['quantity']) && $data['quantity'] < 0) {
            return false;
        }
        if (isset($data['price']) && $data['price'] < 0) {
            return false;
        }

        return true;
    }
}

