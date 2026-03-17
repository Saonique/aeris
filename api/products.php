<?php

// Set headers
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "username"; // replace with your db username
$password = "password"; // replace with your db password
$dbname = "database_name"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['message' => 'Connection failed: ' . $conn->connect_error]));
}

// Handle REST API requests
$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['id'])) {
            get_single_product($_GET['id']);
        } else {
            get_all_products();
        }
        break;
    case 'POST':
        // Handle POST request
        create_product();
        break;
    case 'PUT':
        // Handle PUT request
        update_product();
        break;
    case 'DELETE':
        // Handle DELETE request
        delete_product();
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_all_products() {
    global $conn;
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = [];
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode([]);
    }
}

function get_single_product($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['message' => 'Product not found']);
    }
}

function create_product() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $data['name'], $data['price']);
    $stmt->execute();

    echo json_encode(['message' => 'Product created', 'id' => $stmt->insert_id]);
}

function update_product() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sdi", $data['name'], $data['price'], $data['id']);
    $stmt->execute();

    echo json_encode(['message' => 'Product updated']);
}

function delete_product() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    echo json_encode(['message' => 'Product deleted']);
}

$conn->close();
?>