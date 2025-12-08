
CREATE DATABASE IF NOT EXISTS eventeny CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE eventeny;

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    sale_start_date DATETIME NOT NULL,
    sale_end_date DATETIME NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10, 2) NOT NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    image_path VARCHAR(500) NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_public (is_public),
    INDEX idx_dates (sale_start_date, sale_end_date),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
