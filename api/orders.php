<?php

header('Content-Type: application/json');

// Simulated database for orders
$orders = [];

// Function to handle GET requests for all orders
function getAllOrders() {
    global $orders;
    http_response_code(200);
    echo json_encode($orders);
}

// Function to handle GET requests for a single order
function getOrder($id) {
    global $orders;
    if (isset($orders[$id])) {
        http_response_code(200);
        echo json_encode($orders[$id]);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Order not found']);
    }
}

// Function to handle POST requests to create a new order
function createOrder($data) {
    global $orders;
    $orders[] = $data;
    http_response_code(201);
    echo json_encode(['message' => 'Order created', 'order' => $data]);
}

// Function to handle PUT requests to update an order
function updateOrder($id, $data) {
    global $orders;
    if (isset($orders[$id])) {
        $orders[$id] = array_merge($orders[$id], $data);
        http_response_code(200);
        echo json_encode(['message' => 'Order updated', 'order' => $orders[$id]]);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Order not found']);
    }
}

// Function to handle DELETE requests for an order
function deleteOrder($id) {
    global $orders;
    if (isset($orders[$id])) {
        unset($orders[$id]);
        http_response_code(204);
        echo json_encode(['message' => 'Order deleted']);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Order not found']);
    }
}

// Determine the request method
$request_method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', $_SERVER['REQUEST_URI']);
$order_id = isset($uri[2]) ? (int)$uri[2] : null;

switch ($request_method) {
    case 'GET':
        if ($order_id === null) {
            getAllOrders();
        } else {
            getOrder($order_id);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        createOrder($data);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        updateOrder($order_id, $data);
        break;
    case 'DELETE':
        deleteOrder($order_id);
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}