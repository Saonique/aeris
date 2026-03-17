<?php

// Supplier Management REST API Endpoints

// Database connection
$host = 'localhost';
$db_name = 'your_db_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// Set header for JSON response
header('Content-Type: application/json');

// Get all suppliers
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    $stmt = $pdo->query("SELECT * FROM suppliers");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($suppliers);
}

// Get single supplier
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $supplier_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->execute([$supplier_id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($supplier) {
        echo json_encode($supplier);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Supplier not found']);
    }
}

// Create supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact) VALUES (?, ?)");
    $stmt->execute([$input['name'], $input['contact']]);
    $new_id = $pdo->lastInsertId();
    http_response_code(201);
    echo json_encode(['id' => $new_id]);
}

// Update supplier
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $supplier_id = (int)$_GET['id'];
    $input = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact = ? WHERE id = ?");
    $stmt->execute([$input['name'], $input['contact'], $supplier_id]);
    echo json_encode(['message' => 'Supplier updated successfully']);
}

// Delete supplier
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $supplier_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$supplier_id]);
    echo json_encode(['message' => 'Supplier deleted successfully']);
}

?>