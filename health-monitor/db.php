<?php
// db.php - Configuración de la base de datos

$host = 'localhost';
$dbname = 'health_monitor';
$username = 'root'; // Cambiar por tus credenciales
$password = '';     // Cambiar por tus credenciales

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear tablas si no existen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS patients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL,
            age INT NOT NULL,
            weight DECIMAL(5,2) NOT NULL,
            height DECIMAL(5,2) NOT NULL,
            disease VARCHAR(255),
            disorder VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS heart_rate (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            rate INT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES patients(id)
        );
        
        CREATE TABLE IF NOT EXISTS anomalies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            description TEXT NOT NULL,
            severity ENUM('low', 'medium', 'high') NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES patients(id)
        );
    ");
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>