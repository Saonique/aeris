<?php

header('Content-Type: application/json');

// Include Firebase PHP SDK (Make sure to install it via Composer)
require 'vendor/autoload.php';

use Kreaitirebaseactory;

// Setup Firebase
$factory = (new Factory)->withServiceAccount('path/to/your/firebase_credentials.json');
$messaging = $factory->createMessaging();

// Create a new notification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $notification = [
        'title' => $data['title'],
        'body' => $data['body'],
        'data' => $data['data'] ?? [],
    ];
    // Send the notification using Firebase
    $messaging->send(
        CloudMessage::withTarget('topic', 'notifications')
            ->withNotification($notification)
    );
    echo json_encode(['success' => true]);
    exit;
}

// Get all notifications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch notifications from your database (this is just a placeholder)
    $notifications = [];
    // Example notification data
    // In practice, you would fetch this data from your data source
    $notifications[] = [
        'id' => 1,
        'title' => 'Sample Notification',
        'body' => 'This is a sample notification body.',
    ];
    echo json_encode($notifications);
    exit;
}

// Update a specific notification
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    // Here you would typically update the notification in your database
    echo json_encode(['success' => true, 'id' => $data['id']]);
    exit;
}

// Delete a specific notification
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    // Here you would typically delete the notification from your database
    echo json_encode(['success' => true, 'id' => $data['id']]);
    exit;
}

http_response_code(405); // Method Not Allowed

?>