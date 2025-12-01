<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include "../config/db.php";

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
    // echo json_encode("method: " . $method);
}

switch($method){
    case "POST":
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $sql = "INSERT INTO account (username, password) VALUES ('$username', '$hashedPassword')";
        if (mysqli_query($conn, $sql)) {
            $id = mysqli_insert_id($conn);
            echo json_encode(["status" => "success", "data" => ["ID" => $id, "username" => $username]]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;
    
    default:
        echo json_encode(["status" => "error", "message" => "Unsupported method"]);
        break;
}