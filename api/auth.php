<?php
// API for User Authentication

// Connect to the database
require 'db_connect.php';

// Function for user registration
function register($username, $password) {
    global $conn;
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES (?, ?);";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$username, $hashed_password]);
}

// Function for user login
function login($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // Start session or token generation
        return true;
    }
    return false;
}

// Function for user logout
function logout() {
    // Destroy the session or token
    session_start();
    session_destroy();
}

// Sample usage (uncomment for usage)
// $route = $_SERVER['REQUEST_URI'];
// switch ($route) {
//     case '/login':
//         login($_POST['username'], $_POST['password']);
//         break;
//     case '/register':
//         register($_POST['username'], $_POST['password']);
//         break;
//     case '/logout':
//         logout();
//         break;
// }
?>