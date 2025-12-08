<?php

namespace App\Models;

use App\Database\Connection;

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function getAll($publicOnly = false, $includeDeleted = false)
    {
        $sql = "SELECT * FROM tickets";
        $whereConditions = [];

        if (!$includeDeleted) {
            $whereConditions[] = "deleted_at IS NULL";
        }

        if ($publicOnly) {
            $whereConditions[] = "is_public = 1";
            $whereConditions[] = "sale_start_date <= NOW()";
            $whereConditions[] = "sale_end_date >= NOW()";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql);
    }

    public function getById($id, $includeDeleted = false)
    {
        $sql = "SELECT * FROM tickets WHERE id = ?";
        
        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO tickets (title, sale_start_date, sale_end_date, quantity, price, is_public, image_path, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $params = [
            $data['title'],
            $data['sale_start_date'],
            $data['sale_end_date'],
            $data['quantity'],
            $data['price'],
            $data['is_public'] ?? 1,
            $data['image_path'] ?? null
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        // Build dynamic update query to only update provided fields
        $fields = [];
        $params = [];
        
        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $params[] = $data['title'];
        }
        if (isset($data['sale_start_date'])) {
            $fields[] = "sale_start_date = ?";
            $params[] = $data['sale_start_date'];
        }
        if (isset($data['sale_end_date'])) {
            $fields[] = "sale_end_date = ?";
            $params[] = $data['sale_end_date'];
        }
        if (isset($data['quantity'])) {
            $fields[] = "quantity = ?";
            $params[] = $data['quantity'];
        }
        if (isset($data['price'])) {
            $fields[] = "price = ?";
            $params[] = $data['price'];
        }
        if (isset($data['is_public'])) {
            $fields[] = "is_public = ?";
            $params[] = $data['is_public'];
        }
        if (isset($data['image_path'])) {
            $fields[] = "image_path = ?";
            $params[] = $data['image_path'];
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql = "UPDATE tickets SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $this->db->query($sql, $params);
    }

    public function delete($id)
    {
        $sql = "UPDATE tickets SET deleted_at = NOW(), updated_at = NOW() WHERE id = ? AND deleted_at IS NULL";
        $this->db->query($sql, [$id]);
    }
}

