<?php

// Set content-type to JSON, so the client knows we are returning JSON data
header('Content-Type: application/json');

// Mock data for users and products
$users = [
    ["id" => 1, "name" => "John Doe", "email" => "john@example.com"],
    ["id" => 2, "name" => "Jane Smith", "email" => "jane@example.com"],
    ["id" => 3, "name" => "Alice Johnson", "email" => "alice@example.com"]
];

$products = [
    ["id" => 101, "name" => "Laptop", "price" => 1200],
    ["id" => 102, "name" => "Smartphone", "price" => 800],
    ["id" => 103, "name" => "Tablet", "price" => 450]
];

// Check if the query parameter 'type' is provided
if (isset($_GET['type'])) {
    $type = $_GET['type'];

    // Respond based on the 'type' query parameter
    if ($type === 'users') {
        // Return the list of users in JSON format
        echo json_encode(["status" => "success", "data" => $users]);
    } elseif ($type === 'products') {
        // Return the list of products in JSON format
        echo json_encode(["status" => "success", "data" => $products]);
    } else {
        // Invalid type, return an error message
        echo json_encode(["status" => "error", "message" => "Invalid type. Use 'users' or 'products'."]);
    }
} else {
    // No type provided, return an error message
    echo json_encode(["status" => "error", "message" => "No type provided. Use 'type=users' or 'type=products'."]);
}

?>


//TODO Integrisati microservice na wordpresu




